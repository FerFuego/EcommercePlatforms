<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\Dish;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Mostrar landing page
     */
    public function index()
    {
        $reviews = \App\Models\Review::with(['customer', 'cook.user'])
            ->where('rating', '>=', 4)
            ->latest()
            ->take(9)
            ->get();

        return view('marketplace.landing', compact('reviews'));
    }

    /**
     * Mostrar catálogo de cocineros y platos con mapa
     */
    public function catalog(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $radius = $request->get('radius', 1); // Default 1km
        $diet = $request->get('diet');
        $maxPrice = $request->get('max_price');
        $search = $request->get('search');
        $sort = $request->get('sort', 'distance');

        // Base query
        $cooksQuery = Cook::query()->where('is_approved', true)->where('active', true);

        // Location filter (Nearby scope applies order by distance by default)
        if ($lat && $lng) {
            $cooksQuery->nearby($lat, $lng, $radius);
        }

        // Search filter
        if ($search && mb_strlen($search) >= 3) {
            $cooksQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('dishes', function ($subQ) use ($search) {
                        $subQ->available()->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Diet filter
        if ($diet) {
            $cooksQuery->whereHas('dishes', function ($q) use ($diet) {
                $q->available()->byDiet([$diet]);
            });
        }

        // Price filter
        if ($maxPrice) {
            $cooksQuery->whereHas('dishes', function ($q) use ($maxPrice) {
                $q->available()->where('price', '<=', $maxPrice);
            });
        }

        // Sorting logic closure
        $applySort = function ($query) use ($sort, $lat, $lng) {
            // Priority Listing feature logic (assuming we can join or compute, but since features is JSON
            // the most robust way in Laravel is to join cook_subscriptions and subscription_plans, or sort in memory.
            // Let's sort by distance/rating/price first, then we can apply a secondary sort or handle JSON if MySQL >= 5.7)

            // To prioritize by JSON feature 'priority_listing' in the plan we'd need:
            // LEFT JOIN cook_subscriptions cs ON cs.id = cooks.current_subscription_id AND cs.status = 'active'
            // LEFT JOIN subscription_plans sp ON sp.id = cs.plan_id
            // ORDER BY JSON_EXTRACT(sp.features, '$.priority_listing') DESC

            $query->leftJoin('cook_subscriptions', function ($join) {
                $join->on('cooks.current_subscription_id', '=', 'cook_subscriptions.id')
                    ->where('cook_subscriptions.status', '=', 'active');
            })
                ->leftJoin('subscription_plans', 'cook_subscriptions.plan_id', '=', 'subscription_plans.id')
                ->select('cooks.*');

            if (config('database.default') === 'sqlite') {
                $query->orderByRaw("COALESCE(JSON_EXTRACT(subscription_plans.features, '$.priority_listing'), 'false') DESC");
            } else {
                $query->orderByRaw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(subscription_plans.features, '$.priority_listing')), 'false') DESC");
            }

            if ($sort === 'rating') {
                $query->orderByDesc('rating_avg');
            } elseif ($sort === 'price') {
                $query->withMin('dishes', 'price')->orderBy('dishes_min_price');
            } elseif (!$lat && !$lng) {
                $query->orderByDesc('rating_avg');
            }
        };

        // Save base state before location/sort
        $baseQuery = clone $cooksQuery;

        // 1. Initial Attempt
        if ($lat && $lng) {
            $cooksQuery->nearby($lat, $lng, $radius);
        }
        $applySort($cooksQuery);

        // Check if empty and retry with expanded radius
        $expandedRadius = false;
        if ($cooksQuery->count() === 0 && $lat && $lng && $radius < 50) {
            $retryQuery = clone $baseQuery;
            $retryQuery->nearby($lat, $lng, 50); // Try 50km
            $applySort($retryQuery);

            if ($retryQuery->count() > 0) {
                $cooksQuery = $retryQuery;
                $expandedRadius = true;
                $radius = 50;
            }
        }

        // Clone for map (Get all matching locations without pagination)
        $mapCooks = (clone $cooksQuery)->with('user')->get();

        // Eager load for grid
        $cooks = $cooksQuery->with([
            'user',
            'dishes' => function ($query) use ($diet, $maxPrice) {
                $query->available();

                if ($diet) {
                    $query->byDiet([$diet]);
                }

                if ($maxPrice) {
                    $query->where('price', '<=', $maxPrice);
                }
            }
        ])->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('marketplace.partials.cook-items', compact('cooks'))->render(),
                'next_page_url' => $cooks->nextPageUrl(),
                'mapCooks' => $mapCooks,
                'expandedRadius' => $expandedRadius,
                'newRadius' => $radius
            ]);
        }

        return view('marketplace.catalog', compact('cooks', 'mapCooks', 'lat', 'lng', 'radius', 'expandedRadius'));
    }

    /**
     * Mostrar perfil de un cocinero específico
     */
    public function cookProfile($cookId)
    {
        $cook = Cook::with([
            'dishes' => function ($query) {
                $query->available();
            },
            'reviews.customer',
            'user'
        ])
            ->findOrFail($cookId);

        return view('marketplace.cook-profile', compact('cook'));
    }

    /**
     * Mostrar detalle de un plato
     */
    public function dishDetail($dishId)
    {
        $dish = Dish::with([
            'cook.user',
            'cook.reviews.customer',
            'optionGroups.options'
        ])->findOrFail($dishId);

        if (!$dish->isAvailableToday()) {
            return redirect()->back()->with('error', 'Este plato no está disponible hoy');
        }

        return view('marketplace.dish-detail', compact('dish'));
    }

    /**
     * API endpoint para obtener cocineros cercanos
     */
    public function nearbyCooksApi(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 10);
        $search = $request->input('search');

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Lat/Lng required'], 400);
        }

        $query = Cook::nearby($lat, $lng, $radius)
            ->where('is_approved', true)
            ->where('active', true)
            ->with('user', 'dishes');

        if ($search && mb_strlen($search) >= 3) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('dishes', function ($subQ) use ($search) {
                        $subQ->available()->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $cooks = $query->get();

        // Add calculated distance to each cook
        $cooks = $cooks->map(function ($cook) use ($lat, $lng) {
            $cook->calculated_distance = $this->calculateDistance(
                $lat,
                $lng,
                $cook->location_lat,
                $cook->location_lng
            );
            $cook->delivery_fee = $this->calculateDeliveryFee($cook->calculated_distance);
            return $cook;
        });

        return response()->json($cooks);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Calculate delivery fee based on distance
     * Tiered pricing: 0-2km free, 2-5km $200, 5-10km $400, >10km $600
     */
    private function calculateDeliveryFee($distance)
    {
        if ($distance <= 2) {
            return 0;
        } elseif ($distance <= 5) {
            return 200;
        } elseif ($distance <= 10) {
            return 400;
        } else {
            return 600;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cook;
use App\Models\Dish;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generar dinámicamente el sitemap.xml para motores de búsqueda.
     */
    public function index(): Response
    {
        $urls = [];

        // 1. Páginas estáticas principales
        $urls[] = [
            'loc' => route('home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => route('marketplace.catalog'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];

        // 2. Cocineros aprobados y activos
        $cooks = Cook::where('is_approved', true)
            ->where('active', true)
            ->get();

        foreach ($cooks as $cook) {
            $urls[] = [
                'loc' => route('marketplace.cook.profile', $cook->id),
                'lastmod' => $cook->updated_at ? $cook->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 3. Platos activos de cocineros aprobados
        $dishes = Dish::where('is_active', true)
            ->whereHas('cook', function ($q) {
                $q->where('is_approved', true)->where('active', true);
            })
            ->get();

        foreach ($dishes as $dish) {
            $urls[] = [
                'loc' => route('marketplace.dish.detail', $dish->id),
                'lastmod' => $dish->updated_at ? $dish->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.7',
            ];
        }

        // 4. Páginas legales e institucionales
        $legalPages = [
            'privacy' => route('privacy'),
            'terms' => route('terms'),
            'cookies' => route('cookies'),
        ];

        foreach ($legalPages as $route) {
            $urls[] = [
                'loc' => $route,
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.3',
            ];
        }

        return response()->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}

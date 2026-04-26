<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        $cook = auth()->user()->cook;
        $isPremium = $cook->hasFeature('can_create_offers');

        $broadcasts = \App\Models\CookBroadcast::where('cook_id', $cook->id)->latest()->get();
        $broadcastLimit = 3;
        $broadcastsToday = \App\Models\CookBroadcast::where('cook_id', $cook->id)
            ->whereDate('created_at', today())
            ->count();
        $canCreate = $broadcastsToday < $broadcastLimit;

        return view('cook.broadcasts.index', compact('isPremium', 'broadcasts', 'canCreate', 'broadcastLimit', 'broadcastsToday'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $cook = auth()->user()->cook;
        
        if (!$cook->hasFeature('can_create_offers')) {
            return back()->with('error', 'Necesitas ser Premium para crear campañas.');
        }

        $broadcastsToday = \App\Models\CookBroadcast::where('cook_id', $cook->id)
            ->whereDate('created_at', today())
            ->count();
            
        if ($broadcastsToday >= 3) {
            return back()->with('error', 'Has alcanzado el límite de 3 campañas diarias.');
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // Get all unique customers who have completed orders with this cook and have a phone number
        $customers = \App\Models\User::whereHas('orders', function ($query) use ($cook) {
            $query->where('cook_id', $cook->id)
                  ->whereNotIn('status', ['pending', 'cancelled']);
        })->whereNotNull('phone')
            ->distinct()
            ->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'No tienes clientes con número de teléfono registrado aún.');
        }

        $broadcast = \App\Models\CookBroadcast::create([
            'cook_id' => $cook->id,
            'message' => $request->message,
            'target_audience' => 'all_customers',
            'status' => 'draft',
        ]);

        foreach ($customers as $customer) {
            // Basic phone format cleanup
            $phone = preg_replace('/[^0-9]/', '', $customer->phone);
            
            \App\Models\BroadcastRecipient::create([
                'cook_broadcast_id' => $broadcast->id,
                'user_id' => $customer->id,
                'phone' => $phone,
                'name' => $customer->name,
            ]);
        }

        return redirect()->route('cook.broadcasts.show', $broadcast->id)->with('success', 'Campaña creada. Lista para enviar.');
    }

    public function show($id)
    {
        $cook = auth()->user()->cook;
        $broadcast = \App\Models\CookBroadcast::where('cook_id', $cook->id)->with('recipients')->findOrFail($id);
        
        if ($broadcast->status === 'draft') {
            $broadcast->update(['status' => 'running']);
        }

        return view('cook.broadcasts.show', compact('broadcast'));
    }

    public function markSent($id, $recipientId)
    {
        $cook = auth()->user()->cook;
        $broadcast = \App\Models\CookBroadcast::where('cook_id', $cook->id)->findOrFail($id);
        
        $recipient = \App\Models\BroadcastRecipient::where('cook_broadcast_id', $broadcast->id)->findOrFail($recipientId);
        
        if ($recipient->status === 'pending') {
            $recipient->update(['status' => 'sent']);
            $broadcast->increment('sent_count');
        }

        // Check if all sent
        if ($broadcast->recipients()->where('status', 'pending')->count() === 0) {
            $broadcast->update(['status' => 'completed']);
        }

        return response()->json(['success' => true]);
    }
}

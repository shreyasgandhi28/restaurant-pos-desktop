<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitchenOrderTicket;
use App\Models\Order;
use Illuminate\Http\Request;

class KitchenOrderTicketController extends Controller
{
    /**
     * Get all KOTs for a specific order
     */
    public function index(Order $order)
    {
        $kots = $order->kitchenOrderTickets()
            ->with(['orderItems.menuItem'])
            ->latest()
            ->get();
        
        return response()->json($kots);
    }

    /**
     * Get a specific KOT with details
     */
    public function show(KitchenOrderTicket $kot)
    {
        $kot->load(['orderItems.menuItem', 'order.restaurantTable']);
        return response()->json($kot);
    }

    /**
     * Mark KOT as printed (for kitchen)
     */
    public function print(KitchenOrderTicket $kot)
    {
        // Only update if not already printed
        if (!$kot->printed_at) {
            $kot->update([
                'printed_at' => now(),
                'status' => 'ready', // Using 'ready' status as it's the most appropriate for a printed KOT
            ]);

            // Also update related order items status if needed
            $kot->orderItems()->where('status', 'pending')->update(['status' => 'preparing']);
        }

        $kot->load(['orderItems.menuItem', 'order.restaurantTable']);

        return response()->json([
            'message' => 'KOT marked as printed',
            'kot' => $kot,
        ]);
    }

    /**
     * Update KOT status (for kitchen workflow)
     */
    public function updateStatus(Request $request, KitchenOrderTicket $kot)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served',
        ]);

        $kot->update($validated);

        return response()->json([
            'message' => 'KOT status updated successfully',
            'kot' => $kot,
        ]);
    }

    /**
     * Get all pending KOTs (for kitchen display)
     */
    public function pending()
    {
        $kots = KitchenOrderTicket::with(['orderItems.menuItem', 'order.restaurantTable'])
            ->whereIn('status', ['pending', 'preparing'])
            ->latest()
            ->get();

        return response()->json($kots);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::orderBy('table_number')->get();
        return response()->json($tables);
    }
    
    public function show(RestaurantTable $table)
    {
        $table->load([
            'orders' => function($query) {
                $query->whereNotIn('status', ['served', 'cancelled'])->latest();
            },
            'orders.kitchenOrderTickets.orderItems.menuItem',
            'orders.kitchenOrderTickets' => function($query) {
                $query->latest();
            },
            'orders.orderItems.menuItem' // Load all order items (for old orders without KOTs)
        ]);
        
        // Ensure tax_rate and service_charge_rate are included in the response
        $table->orders->each(function($order) {
            $order->makeVisible(['tax_rate', 'service_charge_rate']);
        });
        
        return response()->json($table);
    }
    
    public function updateStatus(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved',
        ]);

        // Check if this is a manual override or if we should calculate based on orders
        $newStatus = $validated['status'];

        // If manually setting to available but there are active orders, prevent it
        if ($newStatus === 'available' && $table->currentOrder) {
            return response()->json([
                'message' => 'Cannot set table to available while there are active orders',
                'table' => $table,
            ], 400);
        }

        $table->update($validated);

        return response()->json([
            'message' => 'Table status updated successfully',
            'table' => $table,
        ]);
    }

    /**
     * Recalculate all table statuses based on active orders
     */
    public function recalculateStatuses()
    {
        RestaurantTable::recalculateAllStatuses();

        return response()->json([
            'message' => 'All table statuses recalculated successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\KitchenOrderTicket;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['restaurantTable', 'orderItems.menuItem'])
            ->latest()
            ->get();
        
        return response()->json($orders);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Check if there's an active order for this table
            $order = Order::where('restaurant_table_id', $validated['restaurant_table_id'])
                ->whereNotIn('status', ['served', 'cancelled'])
                ->first();
            
            $isNewOrder = !$order;
            
            // Get current rates from settings
            $taxRate = Setting::get('tax_rate', 10);
            $serviceChargeRate = Setting::get('service_charge_rate', 5);
            
            // Create new order if none exists
            if ($isNewOrder) {
                $order = Order::create([
                    'restaurant_table_id' => $validated['restaurant_table_id'],
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'subtotal' => 0,
                    'tax' => 0,
                    'tax_rate' => $taxRate,
                    'service_charge' => 0,
                    'service_charge_rate' => $serviceChargeRate,
                    'total' => 0,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
            
            // Create a new KOT for these items
            $kot = KitchenOrderTicket::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
            
            $kotSubtotal = 0;
            
            // Add items to the KOT
            foreach ($validated['items'] as $item) {
                $totalPrice = $item['unit_price'] * $item['quantity'];
                $kotSubtotal += $totalPrice;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'kitchen_order_ticket_id' => $kot->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'status' => 'pending',
                ]);
            }
            
            // Recalculate order totals using the rates stored with the order
            $orderSubtotal = $order->orderItems()->sum('total_price');
            $tax = $orderSubtotal * ($order->tax_rate / 100);
            $serviceCharge = $orderSubtotal * ($order->service_charge_rate / 100);
            $total = $orderSubtotal + $tax + $serviceCharge;
            
            $order->update([
                'subtotal' => $orderSubtotal,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'total' => $total,
            ]);
            
            // Mark table as occupied
            RestaurantTable::where('id', $validated['restaurant_table_id'])
                ->update(['status' => 'occupied']);
            
            DB::commit();
            
            $kot->load(['orderItems.menuItem']);
            $order->load(['kitchenOrderTickets.orderItems.menuItem', 'restaurantTable']);
            
            return response()->json([
                'message' => $isNewOrder ? 'Order created successfully' : 'Items added to existing order',
                'order' => $order,
                'kot' => $kot,
                'is_new_order' => $isNewOrder,
            ], 201);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function show(Order $order)
    {
        $order->load(['orderItems.menuItem', 'restaurantTable', 'user']);
        return response()->json($order);
    }
    
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        $order->update($validated);
        
        if (in_array($validated['status'], ['served', 'cancelled'])) {
            $order->restaurantTable->update(['status' => 'available']);
        }

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }
    
    public function destroy(Order $order)
    {
        $order->restaurantTable->update(['status' => 'available']);
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }

    /**
     * Complete order and prepare for billing
     */
    public function complete(Order $order)
    {
        DB::beginTransaction();
        try {
            // Mark all KOTs as served
            $order->kitchenOrderTickets()->update(['status' => 'served']);

            // Update order status
            $order->update(['status' => 'served']);

            // Update table status to available since order is completed
            $order->restaurantTable->update(['status' => 'available']);

            DB::commit();

            $order->load(['kitchenOrderTickets.orderItems.menuItem', 'restaurantTable']);

            return response()->json([
                'message' => 'Order completed successfully. Ready for billing.',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to complete order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add items to an existing order
     */
    public function addItems(Request $request, Order $order)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.special_instructions' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create a new KOT for these items
            $kot = KitchenOrderTicket::create([
                'order_id' => $order->id,
                'status' => 'pending',
            ]);

            $kotSubtotal = 0;

            // Add items to the KOT
            foreach ($validated['items'] as $item) {
                $totalPrice = $item['unit_price'] * $item['quantity'];
                $kotSubtotal += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'kitchen_order_ticket_id' => $kot->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'status' => 'pending',
                ]);
            }

            // Recalculate order totals
            $orderSubtotal = $order->orderItems()->sum('total_price');
            $tax = $orderSubtotal * 0.10;
            $serviceCharge = $orderSubtotal * 0.05;
            $total = $orderSubtotal + $tax + $serviceCharge;

            $order->update([
                'subtotal' => $orderSubtotal,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'total' => $total,
            ]);

            DB::commit();

            $kot->load(['orderItems.menuItem']);
            $order->load(['kitchenOrderTickets.orderItems.menuItem', 'restaurantTable']);

            return response()->json([
                'message' => 'Items added successfully',
                'order' => $order,
                'kot' => $kot,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add items',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order item status
     */
    public function updateItemStatus(Request $request, Order $order, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        $orderItem->update($validated);

        // Recalculate order totals after status change
        $this->recalculateOrderTotals($order);

        return response()->json([
            'message' => 'Order item status updated successfully',
            'order_item' => $orderItem,
            'order' => $order->load(['orderItems.menuItem', 'restaurantTable']),
        ]);
    }

    /**
     * Recalculate order totals based on non-cancelled items
     */
    private function recalculateOrderTotals(Order $order)
    {
        // Get all non-cancelled items
        $nonCancelledItems = $order->orderItems()->where('status', '!=', 'cancelled')->get();

        // Calculate new subtotal from non-cancelled items
        $newSubtotal = $nonCancelledItems->sum('total_price');

        // Calculate tax and service charge based on new subtotal
        $tax = $newSubtotal * 0.10; // 10% tax
        $serviceCharge = $newSubtotal * 0.05; // 5% service charge

        // Calculate new total
        $newTotal = $newSubtotal + $tax + $serviceCharge;

        // Update order with new totals
        $order->update([
            'subtotal' => $newSubtotal,
            'tax' => $tax,
            'service_charge' => $serviceCharge,
            'total' => $newTotal,
        ]);

        return $order;
    }
}

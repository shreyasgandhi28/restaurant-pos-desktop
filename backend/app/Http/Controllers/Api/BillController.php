<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Generate bill for an order
     */
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Check if bill already exists
            if ($order->bill) {
                return response()->json([
                    'message' => 'Bill already exists for this order',
                    'bill' => $order->bill,
                ], 400);
            }

            // Calculate bill amounts
            $subtotal = $order->subtotal;
            $taxPercentage = 10; // 10% tax
            $taxAmount = $order->tax;
            $serviceCharge = $order->service_charge;

            $discountPercentage = $validated['discount_percentage'] ?? 0;
            $discountAmount = $validated['discount_amount'] ?? ($subtotal * $discountPercentage / 100);

            $totalAmount = $subtotal + $taxAmount + $serviceCharge - $discountAmount;

            // Create bill
            $bill = Bill::create([
                'order_id' => $order->id,
                'bill_number' => 'BILL-' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_percentage' => $taxPercentage,
                'service_charge' => $serviceCharge,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Mark all non-cancelled order items as 'served'
            $order->orderItems()
                ->where('status', '!=', 'cancelled')
                ->update([
                    'status' => 'served',
                    'updated_at' => now()
                ]);
            
            // Update order status to 'served' if not already
            if ($order->status !== 'served') {
                $order->update(['status' => 'served']);
                
                // Update table status to available since order is completed
                $order->restaurantTable->update(['status' => 'available']);
            }

            DB::commit();

            $bill->load(['order.kitchenOrderTickets.orderItems.menuItem', 'order.restaurantTable']);

            return response()->json([
                'message' => 'Bill generated successfully',
                'bill' => $bill,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to generate bill',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get bill details
     */
    public function show(Bill $bill)
    {
        $bill->load(['order.kitchenOrderTickets.orderItems.menuItem', 'order.restaurantTable']);
        return response()->json($bill);
    }

    /**
     * Mark bill as paid
     */
    public function pay(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,upi,other',
        ]);

        DB::beginTransaction();
        try {
            $bill->update([
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Table is already freed when bill was generated, so no need to free it again
            // But we keep this here for backward compatibility

            DB::commit();

            $bill->load(['order.restaurantTable']);

            return response()->json([
                'message' => 'Payment recorded successfully',
                'bill' => $bill,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all bills
     */
    public function index()
    {
        $bills = Bill::with(['order.restaurantTable'])
            ->latest()
            ->get();

        return response()->json($bills);
    }
}

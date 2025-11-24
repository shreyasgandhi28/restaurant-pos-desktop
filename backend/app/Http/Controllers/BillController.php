<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('order.restaurantTable')
            ->latest()
            ->paginate(20);
        
        return view('bills.index', compact('bills'));
    }

    public function create(Request $request)
    {
        $order = Order::with(['orderItems.menuItem', 'restaurantTable'])
            ->findOrFail($request->order);
        
        // Check if bill already exists
        if ($order->bill) {
            return redirect()->route('bills.show', $order->bill);
        }
        
        // Get settings
        $settings = [
            'tax_rate' => Setting::get('tax_rate', 10),
            'service_charge_rate' => Setting::get('service_charge_rate', 5)
        ];
        
        return view('bills.create', compact('order', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        // Check if bill already exists
        if ($order->bill) {
            return redirect()->route('bills.show', $order->bill)
                ->with('error', 'Bill already exists for this order.');
        }
        
        // Get the subtotal from the order items
        $subtotal = $order->orderItems()->sum(DB::raw('quantity * unit_price'));
        
        // Use the rates that were in effect when the order was created
        $taxAmount = $subtotal * ($order->tax_rate / 100);
        $serviceCharge = $subtotal * ($order->service_charge_rate / 100);
        
        $discountPercentage = $validated['discount_percentage'] ?? 0;
        $discountAmount = $validated['discount_amount'] ?? ($subtotal * ($discountPercentage / 100));
        
        $totalAmount = $subtotal + $taxAmount + $serviceCharge - $discountAmount;
        
        // Update the order with recalculated totals (using original rates)
        $order->update([
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'service_charge' => $serviceCharge,
            'total' => $totalAmount
        ]);
        
        $bill = null;
        
        // Use a transaction to ensure data consistency
        DB::transaction(function () use ($order, $subtotal, $taxAmount, $serviceCharge, $discountPercentage, $discountAmount, $totalAmount, &$bill) {
            // Create the bill
            $bill = Bill::create([
                'order_id' => $order->id,
                'subtotal' => $subtotal,
                'tax_percentage' => $order->tax_rate,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Update all non-cancelled order items to 'served' status
            $order->orderItems()
                ->where('status', '!=', 'cancelled')
                ->update([
                    'status' => 'served',
                    'updated_at' => now()
                ]);
                
            // Also update the order status to 'served' if not already
            if ($order->status !== 'served') {
                $order->update(['status' => 'served']);
                
                // Update table status to available since order is completed
                $order->restaurantTable->update(['status' => 'available']);
            }
        });

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill generated successfully.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['order.orderItems.menuItem', 'order.restaurantTable']);
        
        // Use the rates that were in effect when the order was created
        $taxRate = $bill->order->tax_rate;
        $serviceChargeRate = $bill->order->service_charge_rate;
        
        // Calculate amounts using the order's rates
        $taxAmount = $bill->subtotal * ($taxRate / 100);
        $serviceCharge = $bill->subtotal * ($serviceChargeRate / 100);
        $newTotal = $bill->subtotal + $taxAmount + $serviceCharge - $bill->discount_amount;
        
        return view('bills.show', [
            'bill' => $bill,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'serviceChargeRate' => $serviceChargeRate,
            'serviceCharge' => $serviceCharge,
            'newTotal' => $newTotal
        ]);
    }

    public function edit(Bill $bill)
    {
        return view('bills.edit', compact('bill'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,upi,other',
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        if ($validated['status'] === 'paid' && $bill->status !== 'paid') {
            $validated['paid_at'] = now();
            // Set amount_paid to total_amount when marking as paid
            $validated['amount_paid'] = $bill->total_amount;
            
            // Update order status to served
            $bill->order->update(['status' => 'served']);
            
            // Make table available
            $bill->order->restaurantTable->update(['status' => 'available']);
        }

        $bill->update($validated);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully.');
    }

    public function print(Bill $bill)
    {
        return view('bills.print', ['bill' => $bill]);
    }

    public function preview(Bill $bill)
    {
        $pdf = $this->makeBillPdf($bill);

        return $pdf->stream('bill-' . $bill->bill_number . '.pdf');
    }

    public function download(Bill $bill)
    {
        $pdf = $this->makeBillPdf($bill);

        return $pdf->download('bill-' . $bill->bill_number . '.pdf');
    }

    private function prepareBillData(Bill $bill): array
    {
        $bill->load(['order.orderItems.menuItem', 'order.restaurantTable', 'order.user']);

        $settings = Setting::all()->keyBy('key')->map(function($item) {
            return $item->value;
        });

        $taxRate = $bill->order->tax_rate;
        $serviceChargeRate = $bill->order->service_charge_rate;
        $taxAmount = $bill->subtotal * ($taxRate / 100);
        $serviceCharge = $bill->subtotal * ($serviceChargeRate / 100);
        $newTotal = $bill->subtotal + $taxAmount + $serviceCharge - $bill->discount_amount;

        return [
            'bill' => $bill,
            'settings' => $settings,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'serviceChargeRate' => $serviceChargeRate,
            'serviceCharge' => $serviceCharge,
            'newTotal' => $newTotal,
        ];
    }

    private function makeBillPdf(Bill $bill)
    {
        $data = $this->prepareBillData($bill);

        return Pdf::loadView('bills.pdf', $data)
            ->setPaper([0, 0, 226.77, 841.89], 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'Noto Sans Devanagari')
            ->setOption('fontDir', storage_path('fonts'))
            ->setOption('fontCache', storage_path('fonts'))
            ->setOption('defaultMediaType', 'print')
            ->setOption('dpi', 96);
    }
}

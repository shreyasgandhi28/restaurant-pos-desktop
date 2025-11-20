<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        try {
            $tables = RestaurantTable::orderBy('table_number')->get();

            // Calculate effective status for each table based on active orders
            foreach ($tables as $table) {
                $table->effective_status = $table->effective_status;
            }

            $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
            $menuItems = MenuItem::with('category')->where('is_available', true)->get();
            
            // Get settings
            $settings = Setting::all()->pluck('value', 'key');
            $taxRate = $settings['tax_rate'] ?? 10;
            $serviceChargeRate = $settings['service_charge_rate'] ?? 5;

            return view('pos.index', compact(
                'tables', 
                'categories', 
                'menuItems',
                'taxRate',
                'serviceChargeRate'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in POSController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Error loading POS page: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            
            foreach ($validated['items'] as $item) {
                $subtotal += $item['unit_price'] * $item['quantity'];
            }
            
            $taxRate = Setting::get('tax_rate', 10) / 100;
            $serviceChargeRate = Setting::get('service_charge_rate', 5) / 100;
            
            $tax = $subtotal * $taxRate;
            $serviceCharge = $subtotal * $serviceChargeRate;
            $total = $subtotal + $tax + $serviceCharge;
            
            $order = Order::create([
                'restaurant_table_id' => $validated['restaurant_table_id'],
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);
            
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);
            }
            
            RestaurantTable::where('id', $validated['restaurant_table_id'])
                ->update(['status' => 'occupied']);
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }
}

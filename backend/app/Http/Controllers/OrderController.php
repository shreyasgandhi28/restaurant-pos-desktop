<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bill;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->select('orders.*')
            ->leftJoin('bills', 'orders.id', '=', 'bills.order_id')
            ->leftJoin('restaurant_tables', 'orders.restaurant_table_id', '=', 'restaurant_tables.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->with(['restaurantTable', 'user', 'bill']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('total', 'like', "%{$search}%")
                  ->orWhereHas('restaurantTable', function($tableQuery) use ($search) {
                      $tableQuery->where('table_number', 'like', "%{$search}%");
                  })
                  ->when(stripos('miscellaneous', $search) !== false || stripos('misc', $search) !== false, function($q) {
                      $q->orWhere('type', 'miscellaneous');
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bill', function($billQuery) use ($search) {
                      $billQuery->where('bill_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $paymentStatus = $request->payment_status;
            
            switch ($paymentStatus) {
                case 'unpaid':
                    $query->whereDoesntHave('bill')
                          ->orWhereHas('bill', function($q) {
                              $q->where('status', '!=', 'pending')
                                ->where('amount_paid', '<=', 0);
                          });
                    break;
                case 'pending':
                    $query->whereHas('bill', function($q) {
                        $q->where('status', 'pending')
                          ->where('amount_paid', '<=', 0);
                    });
                    break;
                case 'paid':
                    $query->whereHas('bill', function($q) {
                        $q->where('status', 'paid')
                          ->whereColumn('amount_paid', '>=', 'total_amount');
                    });
                    break;
                case 'partially_paid':
                    $query->whereHas('bill', function($q) {
                        $q->where('status', 'pending')
                          ->where('amount_paid', '>', 0)
                          ->whereColumn('amount_paid', '<', 'total_amount');
                    });
                    break;
                case 'cancelled':
                    $query->whereHas('bill', function($q) {
                        $q->where('status', 'cancelled');
                    });
                    break;
                case 'refunded':
                    $query->whereHas('bill', function($q) {
                        $q->where('status', 'refunded');
                    });
                    break;
            }
        }

        if ($request->filled('table')) {
            $tableSearch = $request->table;
            $query->where(function ($q) use ($tableSearch) {
                $q->whereHas('restaurantTable', function($subQ) use ($tableSearch) {
                    $subQ->where('table_number', 'like', "%{$tableSearch}%");
                });
                
                // If search term matches "misc", include miscellaneous orders
                if (stripos('miscellaneous', $tableSearch) !== false || stripos('misc', $tableSearch) !== false) {
                     $q->orWhereNull('restaurant_table_id')
                       ->orWhere('type', 'miscellaneous');
                }
            });
        }

        if ($request->filled('server')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->server}%");
            });
        }

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sorting
        $sortColumn = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_order', 'desc');

        $allowedSortColumns = [
            'order_number' => 'orders.order_number',
            'bill_number' => 'bills.bill_number',
            'table' => 'restaurant_tables.table_number',
            'server' => 'users.name',
            'payment_status' => 'bills.status',
            'amount_paid' => 'bills.amount_paid',
            'created_at' => 'orders.created_at',
            'total' => 'orders.total',
        ];

        if (array_key_exists($sortColumn, $allowedSortColumns)) {
            $query->orderBy($allowedSortColumns[$sortColumn], $sortDirection);
        } else {
            $query->orderBy('orders.created_at', 'desc');
        }

        $orders = $query->paginate(20)->appends($request->query());

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $table = RestaurantTable::findOrFail($request->table);
        
        if ($table->status !== 'available') {
            return redirect()->route('tables.index')
                ->with('error', 'This table is not available.');
        }
        
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->get();
        
        return view('orders.create', compact('table', 'categories', 'menuItems'));
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
            
            // Calculate subtotal
            foreach ($validated['items'] as $item) {
                $subtotal += $item['unit_price'] * $item['quantity'];
            }
            
            $taxRate = Setting::get('tax_rate', 10) / 100;
            $serviceChargeRate = Setting::get('service_charge_rate', 5) / 100;
            
            $tax = $subtotal * $taxRate;
            $serviceCharge = $subtotal * $serviceChargeRate;
            $total = $subtotal + $tax + $serviceCharge;
            
            // Create order
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
            
            // Create order items
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);
            }
            
            // Update table status
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

    public function show(Order $order)
    {
        $order->load(['orderItems.menuItem.category', 'restaurantTable', 'user', 'bill']);
        
        // Get the current rates from settings
        $settings = Setting::all()->pluck('value', 'key');
        
        // Use the rates from the order if they exist, otherwise use the current settings
        $taxRate = $order->tax_rate ?? ($settings['tax_rate'] ?? 10);
        $serviceChargeRate = $order->service_charge_rate ?? ($settings['service_charge_rate'] ?? 5);
        
        // Calculate the amounts using the order's rates
        $subtotal = $order->orderItems->sum('total_price');
        $tax = $subtotal * ($taxRate / 100);
        $serviceCharge = $subtotal * ($serviceChargeRate / 100);
        $total = $subtotal + $tax + $serviceCharge - ($order->discount ?? 0);
        
        // Update the order with the calculated values
        $order->tax = $tax;
        $order->tax_rate = $taxRate;
        $order->service_charge = $serviceCharge;
        $order->service_charge_rate = $serviceChargeRate;
        $order->total = $total;
        
        return view('orders.show', compact('order'));
    }

    public function pos(Order $order)
    {
        $order->load(['orderItems.menuItem.category', 'restaurantTable', 'user', 'bill', 'kitchenOrderTickets.orderItems.menuItem']);
        
        // Ensure tax_rate and service_charge_rate are included in the response
        $order->makeVisible(['tax_rate', 'service_charge_rate']);

        // Get all categories and menu items for the interface (but won't be interactive)
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->get();

        $tables = RestaurantTable::all();

        return view('orders.pos', compact('order', 'categories', 'menuItems', 'tables'));
    }

    public function edit(Order $order)
    {
        $order->load('orderItems');
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $menuItems = MenuItem::where('is_available', true)->get();
        
        return view('orders.edit', compact('order', 'categories', 'menuItems'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        $order->update($validated);
        
        // If order is served or cancelled, make table available
        if (in_array($validated['status'], ['served', 'cancelled'])) {
            $order->restaurantTable->update(['status' => 'available']);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->restaurantTable->update(['status' => 'available']);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Update the status of an order item.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(OrderItem $orderItem, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        try {
            $orderItem->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order item status updated successfully.',
                'status' => $orderItem->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order item status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createMiscellaneous()
    {
        return view('orders.create-miscellaneous');
    }

    public function storeMiscellaneous(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,upi,other',
            'reason' => 'required|string|max:255',
            'custom_date' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $customDate = $validated['custom_date'] ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['custom_date']) : now();

        DB::beginTransaction();
        try {
            // Create miscellaneous order
            $order = Order::create([
                'restaurant_table_id' => null,
                'user_id' => auth()->id(),
                'status' => 'served', // Immediately served/completed
                'type' => 'miscellaneous',
                'subtotal' => $validated['amount'], // No separate items, just total
                'tax' => 0,
                'tax_rate' => 0,
                'service_charge' => 0,
                'service_charge_rate' => 0,
                'total' => $validated['amount'],
                'notes' => $validated['reason'],
                'order_number' => 'MISC-' . strtoupper(uniqid()),
                'created_at' => $customDate,
                'updated_at' => $customDate,
            ]);

            // Create connected bill
            Bill::create([
                'order_id' => $order->id,
                'bill_number' => 'BILL-' . strtoupper(uniqid()),
                'subtotal' => $validated['amount'],
                'tax_amount' => 0,
                'tax_percentage' => 0,
                'service_charge' => 0,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'total_amount' => $validated['amount'],
                'amount_paid' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
                'paid_at' => $customDate,
                'created_at' => $customDate,
                'updated_at' => $customDate,
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Miscellaneous order added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add miscellaneous order: ' . $e->getMessage());
        }
    }
}

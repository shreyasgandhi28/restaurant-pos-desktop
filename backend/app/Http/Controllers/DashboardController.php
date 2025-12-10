<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use App\Models\Order;
use App\Models\Bill;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getDailyRevenue(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dailyRevenue = [];
        $labels = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $revenue = Bill::where('status', 'paid')
                ->whereDate('paid_at', $date->format('Y-m-d'))
                ->sum('total_amount');
                
            $dailyRevenue[] = $revenue;
            $labels[] = $day;
        }
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $dailyRevenue
        ]);
    }
    
    /**
     * Get peak hours data for all time (overall)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOverallPeakHours()
    {
        try {
            // Initialize hours array with 0 values
            $peakHours = [];
            for ($hour = 10; $hour <= 22; $hour++) {
                $peakHours[$hour] = [
                    'hour' => $hour,
                    'orders' => 0,
                    'label' => $this->formatHourLabel($hour)
                ];
            }

            // Get all orders
            $orders = Order::all(['created_at']);

            // Count orders by hour
            foreach ($orders as $order) {
                $orderHour = (int)$order->created_at->format('H');
                if ($orderHour >= 10 && $orderHour <= 22) {
                    $peakHours[$orderHour]['orders']++;
                }
            }

            // Calculate total days using Carbon for SQLite compatibility
            $firstOrder = Order::orderBy('created_at', 'asc')->first();
            $lastOrder = Order::orderBy('created_at', 'desc')->first();
            
            $totalDays = 1; // Default to 1 to avoid division by zero
            if ($firstOrder && $lastOrder) {
                $totalDays = $firstOrder->created_at->diffInDays($lastOrder->created_at) + 1;
            }

            $labels = [];
            $data = [];
            
            foreach ($peakHours as $hour) {
                $labels[] = $hour['label'];
                $data[] = round($hour['orders'] / $totalDays, 1);
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data,
                'label' => 'Average Orders per Hour (Overall)'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting overall peak hours: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load peak hours data.'
            ], 500);
        }
    }

    /**
     * Get peak hours data for a specific date
     *
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPeakHoursByDate($date)
    {
        try {
            $date = Carbon::parse($date)->format('Y-m-d');
            
            // Initialize hours array with 0 values
            $peakHours = [];
            for ($hour = 10; $hour <= 22; $hour++) {
                $peakHours[$hour] = [
                    'hour' => $hour,
                    'orders' => 0,
                    'label' => $this->formatHourLabel($hour)
                ];
            }

            // Get orders for the specific date
            $orders = Order::whereDate('created_at', $date)->get(['created_at']);

            // Count orders by hour
            foreach ($orders as $order) {
                $orderHour = (int)$order->created_at->format('H');
                if ($orderHour >= 10 && $orderHour <= 22) {
                    $peakHours[$orderHour]['orders']++;
                }
            }

            $labels = [];
            $data = [];
            
            foreach ($peakHours as $hour) {
                $labels[] = $hour['label'];
                $data[] = $hour['orders'];
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data,
                'label' => 'Orders on ' . Carbon::parse($date)->format('M d, Y')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting peak hours by date: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load peak hours data for the selected date.'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'daily'); // daily, monthly, yearly
        $dateRange = $this->getDateRange($period);
        
        // Get current year and month for the graph
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Generate years for the year dropdown (last 5 years and next 5 years)
        $years = range($currentYear - 5, $currentYear + 5);
        
        // Get available dates with orders for the date dropdown
        $availableDates = [];
        $datesWithOrders = Order::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30) // Limit to last 30 days for performance
            ->get();
            
        foreach ($datesWithOrders as $date) {
            $carbonDate = Carbon::parse($date->date);
            $formattedDate = $carbonDate->format('Y-m-d');
            $availableDates[$formattedDate] = $carbonDate->format('M d, Y');
        }
        
        // Generate months for the month dropdown
        $months = [
            ['value' => '01', 'name' => 'January'],
            ['value' => '02', 'name' => 'February'],
            ['value' => '03', 'name' => 'March'],
            ['value' => '04', 'name' => 'April'],
            ['value' => '05', 'name' => 'May'],
            ['value' => '06', 'name' => 'June'],
            ['value' => '07', 'name' => 'July'],
            ['value' => '08', 'name' => 'August'],
            ['value' => '09', 'name' => 'September'],
            ['value' => '10', 'name' => 'October'],
            ['value' => '11', 'name' => 'November'],
            ['value' => '12', 'name' => 'December'],
        ];

        // Basic metrics
        $totalTables = RestaurantTable::count();
        $availableTables = RestaurantTable::where('status', 'available')->count();
        $activeOrders = Order::whereIn('status', ['pending', 'preparing', 'ready'])->count();

        $todayRevenue = Bill::where('status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('total_amount');

        $recentOrders = Order::with('restaurantTable')
            ->latest()
            ->take(5)
            ->get();

        // Revenue analytics - simplified for the new format
        $revenueData = $this->getRevenueData($period, $dateRange);

        // Popular items
        $popularItems = $this->getPopularItems($dateRange);

        // Peak hours
        $peakHours = $this->getPeakHours($dateRange);

        // Monthly revenue data
        $monthlyRevenue = $this->getMonthlyRevenue(6); // Last 6 months
        
        // Payment method breakdown for today
        $todayPaymentBreakdown = $this->getPaymentBreakdown('daily');
        
        // Payment method breakdown for month
        $monthlyPaymentBreakdown = $this->getPaymentBreakdown('monthly');
        
        // Payment method breakdown for year
        $yearlyPaymentBreakdown = $this->getPaymentBreakdown('yearly');

        return view('dashboard', compact(
            'totalTables',
            'availableTables',
            'activeOrders',
            'todayRevenue',
            'todayPaymentBreakdown',
            'monthlyPaymentBreakdown',
            'yearlyPaymentBreakdown',
            'recentOrders',
            'revenueData',
            'popularItems',
            'peakHours',
            'monthlyRevenue',
            'period',
            'months',
            'years',
            'currentYear',
            'currentMonth',
            'availableDates'
        ));
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'daily':
                return [
                    'start' => Carbon::now()->startOfDay(),
                    'end' => Carbon::now()->endOfDay()
                ];
            case 'yearly':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear()
                ];
            case 'monthly':
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
        }
    }

    private function getRevenueData($period, $dateRange)
    {
        // Daily revenue (today)
        $dailyRevenue = Bill::where('status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('total_amount');

        // Monthly revenue (current month)
        $monthlyRevenue = Bill::where('status', 'paid')
            ->whereYear('paid_at', Carbon::now()->year)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('total_amount');

        // Yearly revenue (current year)
        $yearlyRevenue = Bill::where('status', 'paid')
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('total_amount');

        return [
            'daily' => $dailyRevenue,
            'monthly' => $monthlyRevenue,
            'yearly' => $yearlyRevenue
        ];
    }

    private function getRevenueComparison($period)
    {
        $currentPeriod = $this->getDateRange($period);

        // Get previous period for comparison
        $previousPeriod = [
            'start' => Carbon::parse($currentPeriod['start'])->sub(1, $period === 'yearly' ? 'year' : ($period === 'monthly' ? 'month' : 'day')),
            'end' => Carbon::parse($currentPeriod['end'])->sub(1, $period === 'yearly' ? 'year' : ($period === 'monthly' ? 'month' : 'day'))
        ];

        $currentRevenue = Bill::where('status', 'paid')
            ->whereBetween('paid_at', [$currentPeriod['start'], $currentPeriod['end']])
            ->sum('total_amount');

        $previousRevenue = Bill::where('status', 'paid')
            ->whereBetween('paid_at', [$previousPeriod['start'], $previousPeriod['end']])
            ->sum('total_amount');

        $percentageChange = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        return [
            'current' => $currentRevenue,
            'previous' => $previousRevenue,
            'change' => $percentageChange
        ];
    }

    private function getPopularItems($dateRange, $limit = 10)
    {
        try {
            return OrderItem::select('menu_item_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(total_price) as total_revenue'))
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('bills', 'orders.id', '=', 'bills.order_id')
                ->where('bills.status', 'paid')
                ->where('order_items.status', '!=', 'cancelled')
                ->groupBy('menu_item_id')
                ->orderBy('total_quantity', 'desc')
                ->limit($limit)
                ->with(['menuItem' => function($query) {
                    $query->with('category');
                }])
                ->get()
                ->map(function($item) {
                    // Ensure we have a menu item before proceeding
                    if ($item->menuItem) {
                        return $item;
                    }
                    return null;
                })
                ->filter() // Remove any null values
                ->values(); // Reset array keys
        } catch (\Exception $e) {
            \Log::error('Error getting popular items: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getOrderTrends($period, $dateRange)
    {
        try {
            $data = [];
            $labels = [];

            if ($period === 'yearly') {
                for ($i = 1; $i <= 12; $i++) {
                    $month = Carbon::create(null, $i, 1);
                    $count = Order::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $i)
                        ->count();

                    $labels[] = $month->format('M');
                    $data[] = $count;
                }
            } elseif ($period === 'monthly') {
                $startDate = $dateRange['start'];
                $endDate = $dateRange['end'];

                for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                    $count = Order::whereDate('created_at', $date->toDateString())->count();

                    $labels[] = $date->format('d');
                    $data[] = $count;
                }
            } else {
                $startDate = $dateRange['start'];
                $endDate = $dateRange['end'];

                for ($date = $startDate; $date <= $endDate; $date->addDay()) {
                    $count = Order::whereDate('created_at', $date->toDateString())->count();

                    $labels[] = $date->format('D');
                    $data[] = $count;
                }
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting order trends: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    private function getPeakHours($dateRange)
    {
        try {
            $peakHours = [];
            $startDate = $dateRange['start']->copy();
            $endDate = $dateRange['end']->copy();

            // Initialize hours array with 0 values
            for ($hour = 10; $hour <= 22; $hour++) {
                $peakHours[$hour] = [
                    'hour' => $hour,
                    'orders' => 0,
                    'label' => $this->formatHourLabel($hour)
                ];
            }

            // Get all orders within the date range
            $orders = Order::whereBetween('created_at', [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            ])->get(['created_at']);

            // Count orders by hour
            foreach ($orders as $order) {
                $orderHour = (int)$order->created_at->format('H');
                if ($orderHour >= 10 && $orderHour <= 22) {
                    $peakHours[$orderHour]['orders']++;
                }
            }

            // Convert to sequential array and calculate average per day
            $days = $startDate->diffInDays($endDate) ?: 1; // Avoid division by zero
            return array_map(function($item) use ($days) {
                return [
                    'hour' => $item['hour'],
                    'orders' => $item['orders'],
                    'average_orders' => round($item['orders'] / $days, 1),
                    'label' => $item['label']
                ];
            }, array_values($peakHours));
        } catch (\Exception $e) {
            \Log::error('Error getting peak hours: ' . $e->getMessage());
            
            // Return empty data structure with all hours if there's an error
            $peakHours = [];
            for ($hour = 10; $hour <= 22; $hour++) {
                $peakHours[] = [
                    'hour' => $hour,
                    'orders' => 0,
                    'label' => $this->formatHourLabel($hour)
                ];
            }
            return $peakHours;
        }
    }

    private function formatHourLabel($hour)
    {
        if ($hour === 0) {
            return '12:00 AM';
        } elseif ($hour < 12) {
            return sprintf('%d:00 AM', $hour);
        } elseif ($hour === 12) {
            return '12:00 PM';
        } else {
            return sprintf('%d:00 PM', $hour - 12);
        }
    }

    private function getMonthlyRevenue($months = 6)
    {
        $revenueData = [];
        $labels = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
                
            $revenueData[] = $revenue;
            $labels[] = $date->format('M Y');
        }
        
        return [
            'data' => $revenueData,
            'labels' => $labels
        ];
    }

    /**
     * Get monthly revenue data for a specific year
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthlyRevenueData(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $revenueData = [];
        $labels = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $revenue = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            // Calculate payment breakdown for the month
            $breakdown = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->selectRaw('payment_method, sum(total_amount) as total')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray();
                
            // Ensure all payment methods exist
            $completeBreakdown = [];
            $paymentMethods = ['cash', 'card', 'upi', 'other'];
            foreach ($paymentMethods as $method) {
                $completeBreakdown[$method] = (float)($breakdown[$method] ?? 0);
            }
                
            $revenueData[] = [
                'total' => (float)$revenue,
                'breakdown' => $completeBreakdown
            ];
            $labels[] = $monthStart->format('M Y');
        }
        
        return response()->json([
            'success' => true,
            'data' => array_column($revenueData, 'total'),
            'breakdown' => array_column($revenueData, 'breakdown'),
            'labels' => $labels
        ]);
    }

    private function getTableUtilization($dateRange)
    {
        $tables = RestaurantTable::all();
        $utilization = [];

        foreach ($tables as $table) {
            $ordersCount = Order::where('restaurant_table_id', $table->id)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count();

            $utilizationRate = $table->capacity > 0 ? ($ordersCount / $table->capacity) * 100 : 0;

            $utilization[] = [
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'orders_count' => $ordersCount,
                'utilization_rate' => round($utilizationRate, 1)
            ];
        }

        return $utilization;
    }

    private function getPaymentBreakdown($period)
    {
        $query = Bill::where('status', 'paid');
        
        if ($period === 'daily') {
            $query->whereDate('paid_at', today());
        } elseif ($period === 'monthly') {
            $query->whereYear('paid_at', Carbon::now()->year)
                ->whereMonth('paid_at', Carbon::now()->month);
        } elseif ($period === 'yearly') {
            $query->whereYear('paid_at', Carbon::now()->year);
        }
        
        $breakdown = $query->selectRaw('payment_method, sum(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();
            
        // Ensure all payment methods exist
        $paymentMethods = ['cash', 'card', 'upi', 'other'];
        foreach ($paymentMethods as $method) {
            if (!isset($breakdown[$method])) {
                $breakdown[$method] = 0;
            }
        }
        
        return $breakdown;
    }
}


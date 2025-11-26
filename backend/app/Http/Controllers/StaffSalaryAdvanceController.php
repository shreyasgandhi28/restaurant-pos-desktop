<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffSalaryAdvanceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = SalaryAdvance::with('employee')
                ->latest();

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })->orWhere('notes', 'like', "%{$search}%");
                });
            }

            // Apply date filter
            if ($request->filled('date')) {
                $query->whereDate('advance_date', $request->date);
            }

            // Apply staff filter
            if ($request->filled('staff_id')) {
                $query->where('employee_id', $request->staff_id);
            }

            // Apply payment method filter
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            $advances = $query->paginate(15)->appends($request->query());

            $staff = Employee::select('id', 'name')
                ->orderBy('name')
                ->get();

            // Calculate summary data
            $totalQuery = clone $query;
            $totalAdvances = $totalQuery->sum('amount');

            $monthlyQuery = clone $query;
            $monthlyAdvances = $monthlyQuery->whereYear('advance_date', now()->year)
                ->whereMonth('advance_date', now()->month)
                ->sum('amount');

            return view('staff.salary-advances.index', [
                'advances' => $advances,
                'staff' => $staff,
                'total_advances' => $totalAdvances,
                'monthly_advances' => $monthlyAdvances,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in StaffSalaryAdvanceController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'An error occurred while loading the page. Please try again.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'notes' => 'nullable|string|max:1000',
            'advance_date' => 'required|date',
        ]);

        try {
            $advance = DB::transaction(function () use ($validated) {
                return SalaryAdvance::create([
                    'employee_id' => $validated['employee_id'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['notes'] ?? null,
                    'advance_date' => $validated['advance_date']
                ]);
            });

            return redirect()->route('staff-salary-advances.index')
                ->with('success', 'Salary advance recorded successfully');
        } catch (\Exception $e) {
            Log::error('Error recording salary advance: ' . $e->getMessage());
            return back()->with('error', 'Failed to record salary advance. Please try again.');
        }
    }

    public function show(SalaryAdvance $advance)
    {
        $advance->load('employee');
        
        return view('staff.salary-advances.show', [
            'advance' => $advance
        ]);
    }

    public function edit(SalaryAdvance $advance)
    {
        $staff = Employee::select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return view('staff.salary-advances.edit', [
            'advance' => $advance,
            'staff' => $staff,
            'paymentMethods' => ['cash', 'bank_transfer', 'cheque', 'other']
        ]);
    }

    public function update(Request $request, SalaryAdvance $advance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'notes' => 'nullable|string|max:1000',
            'advance_date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        try {
            $advance->update([
                'employee_id' => $validated['employee_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'advance_date' => $validated['advance_date'],
                'status' => $validated['status'],
                'approved_by' => $validated['status'] === 'approved' ? auth()->id() : null,
            ]);

            return redirect()->route('staff-salary-advances.show', $advance)
                ->with('success', 'Salary advance updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Error updating salary advance: ' . $e->getMessage());
            return back()->with('error', 'Failed to update salary advance. Please try again.');
        }
    }

    public function summary()
    {
        $summary = Employee::withSum(['salaryAdvances as total_advances' => function ($query) {
                $query->where('status', 'approved');
            }], 'amount')
            ->select('id', 'name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_advances' => (float) $user->total_advances,
                ];
            });

        return response()->json($summary);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::orderBy('table_number')->get();

        // Calculate effective status for each table based on active orders
        foreach ($tables as $table) {
            $table->effective_status = $table->effective_status;
        }

        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:restaurant_tables',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function show(RestaurantTable $table)
    {
        $table->load(['orders' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('tables.show', compact('table'));
    }

    public function edit(RestaurantTable $table)
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:restaurant_tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Table deleted successfully.');
    }
}

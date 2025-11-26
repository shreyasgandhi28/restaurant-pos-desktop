<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Employee::latest()->get();
        
        // Define roles for the dropdown (just labels now)
        $roles = collect([
            (object)['name' => 'waiter'],
            (object)['name' => 'manager'],
            (object)['name' => 'chef'],
            (object)['name' => 'cleaner'],
        ]);

        return view('staff.index', [
            'staff' => $staff,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'role' => 'required|string|max:50',
        ]);

        Employee::create($validated);

        return back()->with('success', 'Staff member added successfully.');
    }

    public function edit(Employee $staff)
    {
        $roles = collect([
            (object)['name' => 'waiter'],
            (object)['name' => 'manager'],
            (object)['name' => 'chef'],
            (object)['name' => 'cleaner'],
        ]);

        return view('staff.edit', [
            'staff' => $staff,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, Employee $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'role' => 'required|string|max:50',
        ]);

        $staff->update($validated);

        return back()->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Employee $staff)
    {
        $staff->delete();
        return back()->with('success', 'Staff member deleted successfully.');
    }
}

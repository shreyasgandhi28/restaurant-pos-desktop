<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::role(['waiter', 'manager'])
            ->where('name', '!=', 'Staff User')
            ->select('id', 'name', 'email', 'created_at')
            ->with('roles')
            ->latest()
            ->get();

        $roles = Role::whereIn('name', ['waiter', 'manager'])->get(['id', 'name']);

        return view('staff.index', [
            'staff' => $staff,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:waiter,manager',
        ]);

        $password = Str::random(8);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
        ]);

        $user->assignRole($validated['role']);

        // In a real application, you might want to email the user their password
        // Mail::to($user)->send(new NewStaffAccount($user, $password));

        return back()->with('success', 'Staff member added successfully.');
    }

    public function destroy(User $staff)
    {
        // Prevent deleting the currently authenticated user
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $staff->delete();
        return back()->with('success', 'Staff member deleted successfully.');
    }
}

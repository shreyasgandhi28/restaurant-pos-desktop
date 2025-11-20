<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
            'restaurant_name' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'business_address' => 'nullable|string|max:500',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
        ]);

        // Update basic settings
        Setting::set('tax_rate', $validated['tax_rate'], 'number', 'GST/Tax percentage');
        Setting::set('service_charge_rate', $validated['service_charge_rate'], 'number', 'Service charge percentage');
        Setting::set('restaurant_name', $validated['restaurant_name'], 'string', 'Restaurant name');
        
        // Update business details
        Setting::set('gst_number', $validated['gst_number'] ?? '', 'string', 'Business GST number');
        Setting::set('business_address', $validated['business_address'] ?? '', 'string', 'Business full address');
        Setting::set('primary_phone', $validated['primary_phone'] ?? '', 'string', 'Primary contact number (required)');
        Setting::set('secondary_phone', $validated['secondary_phone'] ?? '', 'string', 'Secondary contact number (optional)');

        return back()->with('success', 'Settings updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        
        $query = MenuItem::with('category');
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        $menuItems = $query->paginate(12);
        
        return view('menu-items.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('menu-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'preparation_time' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($validated);

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        return view('menu-items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('menu-items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'preparation_time' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                \Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($validated);

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image) {
            \Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item deleted successfully.');
    }
}

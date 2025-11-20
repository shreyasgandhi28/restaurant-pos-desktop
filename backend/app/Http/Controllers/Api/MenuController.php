<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category')->where('is_available', true);
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $menuItems = $query->get();
        
        return response()->json($menuItems);
    }
    
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('category');
        return response()->json($menuItem);
    }
    
    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('menuItems')
            ->get();
        
        return response()->json($categories);
    }
}

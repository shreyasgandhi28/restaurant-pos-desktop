<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\MenuItem;

class CleanupOldMenuSeeder extends Seeder
{
    public function run()
    {
        // List of old categories to remove
        $oldCategories = [
            'Starters',
            'Main Course',
            'Desserts',
            'Beverages',
            'Soups',
            'Salads',
            'Bread',
            'Rice & Noodles',
            'Chinese',
            'Italian',
            'Mexican',
            'Indian',
            'Appetizers',
            'Main Dishes',
            'Sides',
            'Drinks'
        ];

        // Find and delete old categories and their menu items
        $deletedCount = 0;
        foreach ($oldCategories as $categoryName) {
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                // Delete all menu items in this category
                MenuItem::where('category_id', $category->id)->delete();
                // Delete the category
                $category->delete();
                $deletedCount++;
            }
        }

        $this->command->info("Removed $deletedCount old categories and their menu items.");
    }
}

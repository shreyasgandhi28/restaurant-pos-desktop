<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and permissions
        $this->call([
            RoleAndPermissionSeeder::class,
            MarathiMenuSeeder::class,  // Add Marathi menu items
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // Manager user removed as per request to keep only Admin and Staff

        // Create Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@restaurant.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );
        $staff->syncRoles(['staff']);

        // Create Categories
        $categories = [
            ['name' => 'Starters', 'slug' => 'starters', 'description' => 'Appetizers and starters'],
            ['name' => 'Main Course', 'slug' => 'main-course', 'description' => 'Main dishes'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats'],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Drinks and beverages'],
        ];

        foreach ($categories as $index => $categoryData) {
            Category::create(array_merge($categoryData, ['sort_order' => $index + 1]));
        }

        // Create Sample Menu Items (Prices in Indian Rupees)
        $menuItems = [
            ['category' => 'starters', 'name' => 'Paneer Tikka', 'price' => 250, 'description' => 'Grilled cottage cheese with spices'],
            ['category' => 'starters', 'name' => 'Garlic Bread', 'price' => 150, 'description' => 'Toasted bread with garlic butter'],
            ['category' => 'main-course', 'name' => 'Butter Chicken', 'price' => 350, 'description' => 'Creamy tomato-based chicken curry'],
            ['category' => 'main-course', 'name' => 'Dal Makhani', 'price' => 280, 'description' => 'Black lentils in creamy gravy'],
            ['category' => 'main-course', 'name' => 'Biryani', 'price' => 320, 'description' => 'Aromatic rice with spices and meat'],
            ['category' => 'desserts', 'name' => 'Gulab Jamun', 'price' => 120, 'description' => 'Sweet milk dumplings in sugar syrup'],
            ['category' => 'desserts', 'name' => 'Ice Cream', 'price' => 100, 'description' => 'Vanilla ice cream'],
            ['category' => 'beverages', 'name' => 'Masala Chai', 'price' => 50, 'description' => 'Indian spiced tea'],
            ['category' => 'beverages', 'name' => 'Fresh Lime Soda', 'price' => 80, 'description' => 'Refreshing lime drink'],
        ];

        foreach ($menuItems as $item) {
            $category = Category::where('slug', $item['category'])->first();
            MenuItem::create([
                'category_id' => $category->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'price' => $item['price'],
                'is_available' => true,
            ]);
        }

        // Create Restaurant Tables
        for ($i = 1; $i <= 12; $i++) {
            RestaurantTable::create([
                'table_number' => 'T' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'capacity' => rand(2, 6),
                'status' => 'available',
            ]);
        }

        // Create Default Settings
        Setting::create([
            'key' => 'tax_rate',
            'value' => '10',
            'type' => 'number',
            'description' => 'GST/Tax percentage',
        ]);

        Setting::create([
            'key' => 'service_charge_rate',
            'value' => '5',
            'type' => 'number',
            'description' => 'Service charge percentage',
        ]);

        Setting::create([
            'key' => 'restaurant_name',
            'value' => 'Restaurant POS',
            'type' => 'string',
            'description' => 'Restaurant name',
        ]);

        Setting::create([
            'key' => 'currency_symbol',
            'value' => '₹',
            'type' => 'string',
            'description' => 'Currency symbol',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'number', 'description' => 'GST/Tax percentage'],
            ['key' => 'service_charge_rate', 'value' => '5', 'type' => 'number', 'description' => 'Service charge percentage'],
            ['key' => 'restaurant_name', 'value' => 'Restaurant POS', 'type' => 'string', 'description' => 'Restaurant name'],
            ['key' => 'currency_symbol', 'value' => '₹', 'type' => 'string', 'description' => 'Currency symbol'],
            ['key' => 'gst_number', 'value' => '', 'type' => 'string', 'description' => 'Business GST number'],
            ['key' => 'business_address', 'value' => '', 'type' => 'string', 'description' => 'Business full address'],
            ['key' => 'primary_phone', 'value' => '', 'type' => 'string', 'description' => 'Primary contact number (required)'],
            ['key' => 'secondary_phone', 'value' => '', 'type' => 'string', 'description' => 'Secondary contact number (optional)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

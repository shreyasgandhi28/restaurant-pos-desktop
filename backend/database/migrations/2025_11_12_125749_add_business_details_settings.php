<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'gst_number',
                'value' => '',
                'type' => 'string',
                'description' => 'Business GST number',
            ],
            [
                'key' => 'business_address',
                'value' => '',
                'type' => 'string',
                'description' => 'Business full address',
            ],
            [
                'key' => 'primary_phone',
                'value' => '',
                'type' => 'string',
                'description' => 'Primary contact number (required)',
            ],
            [
                'key' => 'secondary_phone',
                'value' => '',
                'type' => 'string',
                'description' => 'Secondary contact number (optional)',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = ['gst_number', 'business_address', 'primary_phone', 'secondary_phone'];
        Setting::whereIn('key', $keys)->delete();
    }
};

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
                'key' => 'tax_rate',
                'value' => '10',
                'type' => 'number',
                'description' => 'GST/Tax percentage',
            ],
            [
                'key' => 'service_charge_rate',
                'value' => '5',
                'type' => 'number',
                'description' => 'Service charge percentage',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = ['tax_rate', 'service_charge_rate'];
        Setting::whereIn('key', $keys)->delete();
    }
};

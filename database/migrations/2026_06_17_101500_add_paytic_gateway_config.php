<?php

use App\Model\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $exists = Setting::where('key_name', 'paytic')
            ->where('settings_type', 'payment_config')
            ->exists();

        if (!$exists) {
            Setting::create([
                'key_name' => 'paytic',
                'live_values' => [
                    'api_key' => '',
                    'base_url' => 'https://pay.tic.bd/api',
                    'mode' => 'live',
                    'status' => 1,
                ],
                'test_values' => [
                    'api_key' => '',
                    'base_url' => 'https://pay.tic.bd/api',
                    'mode' => 'live',
                    'status' => 1,
                ],
                'settings_type' => 'payment_config',
                'mode' => 'live',
                'is_active' => 1,
                'additional_data' => json_encode([
                    'gateway_title' => 'PayTic',
                    'gateway_image' => '',
                ]),
            ]);
        }
    }

    public function down(): void
    {
        Setting::where('key_name', 'paytic')
            ->where('settings_type', 'payment_config')
            ->delete();
    }
};

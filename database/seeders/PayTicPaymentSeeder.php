<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayTicPaymentSeeder extends Seeder
{
    public function run()
    {
        $exists = DB::table('addon_settings')->where('key_name', 'paytic')->exists();

        if (!$exists) {
            DB::table('addon_settings')->insert([
                'id' => Str::uuid(),
                'key_name' => 'paytic',
                'live_values' => json_encode([
                    'api_key' => env('PAYTIC_API_KEY', ''),
                    'base_url' => env('PAYTIC_BASE_URL', 'https://pay.tic.bd/api'),
                ]),
                'test_values' => json_encode([
                    'api_key' => env('PAYTIC_API_KEY', ''),
                    'base_url' => env('PAYTIC_BASE_URL', 'https://pay.tic.bd/api'),
                ]),
                'settings_type' => 'payment_config',
                'mode' => 'live',
                'is_active' => 1,
                'additional_data' => json_encode([
                    'gateway_title' => 'PayTic',
                    'gateway_image' => '',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "PayTic payment gateway registered successfully!\n";
        } else {
            echo "PayTic payment gateway already exists.\n";
        }
    }
}

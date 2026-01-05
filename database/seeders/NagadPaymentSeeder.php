<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NagadPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if Nagad already exists
        $exists = DB::table('addon_settings')->where('key_name', 'nagad')->exists();

        if (!$exists) {
            DB::table('addon_settings')->insert([
                'id' => Str::uuid(),
                'key_name' => 'nagad',
                'live_values' => json_encode([
                    'merchant_id' => env('NAGAD_MERCHANT_ID', ''),
                    'merchant_number' => env('NAGAD_MERCHANT_NUMBER', ''),
                    'public_key' => env('NAGAD_PUBLIC_KEY', ''),
                    'private_key' => env('NAGAD_PRIVATE_KEY', ''),
                    'callback_url' => route('nagad.callback'),
                ]),
                'test_values' => json_encode([
                    'merchant_id' => env('NAGAD_TEST_MERCHANT_ID', ''),
                    'merchant_number' => env('NAGAD_TEST_MERCHANT_NUMBER', ''),
                    'public_key' => env('NAGAD_TEST_PUBLIC_KEY', ''),
                    'private_key' => env('NAGAD_TEST_PRIVATE_KEY', ''),
                    'callback_url' => route('nagad.callback'),
                ]),
                'settings_type' => 'payment_config',
                'mode' => 'test', // Default to test mode
                'is_active' => 1,
                'additional_data' => json_encode([
                    'gateway_title' => 'Nagad',
                    'gateway_image' => 'nagad.png',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "Nagad payment gateway registered successfully!\n";
        } else {
            echo "Nagad payment gateway already exists.\n";
        }
    }
}

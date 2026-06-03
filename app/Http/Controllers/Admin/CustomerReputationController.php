<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\CPU\Helpers;

class CustomerReputationController extends Controller
{
    public function checkCustomerReputationStatus($phone)
    {
        $apiKey = Helpers::get_business_settings('fraudshield_api_key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'FraudShield API key is not configured',
                'Summaries' => []
            ], 400);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://fraudshield.bd/api/customer/check', [
            'phone' => $phone
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $summaries = [];

            $couriers = ['steadfast', 'pathao', 'redx', 'paperfly', 'carrybee', 'parceldex'];
            foreach ($couriers as $key) {
                if (isset($data['courierData'][$key])) {
                    $c = $data['courierData'][$key];
                    $summaries[$c['name']] = [
                        'Total Parcels' => $c['total_parcel'],
                        'Delivered Parcels' => $c['success_parcel'],
                        'Canceled Parcels' => $c['cancelled_parcel'],
                    ];
                }
            }

            return response()->json(['Summaries' => $summaries]);
        }

        return response()->json([
            'error' => 'Failed to fetch data from FraudShield',
            'Summaries' => []
        ], $response->status());
    }
}

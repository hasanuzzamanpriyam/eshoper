<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;



class SteadFastController extends Controller
{
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'sctcmp7aiidcqlz4dqypuozwcz4uttcv';
        $this->secretKey = '9zabieyfnhd8mnvl8ldpootl';
        $this->baseUrl = 'https://portal.packzy.com/api/v1';
    }

// steadfast get data
    public function steadFastApiGetData($apiEndPoint)
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . $apiEndPoint);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


 // send single product
    public function sendSinglePorduct($id){
        $singleProduct = DB::table('orders')->where('id', $id)->first();
        if($singleProduct){

            $ShippingAddress=$singleProduct->shipping_address_data;
            $data = json_decode($ShippingAddress, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {

                $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json'
                ])->post($this->baseUrl.'/create_order', [
                    'invoice' => $singleProduct->id,
                    'recipient_name' => $data['contact_person_name'],
                    'recipient_phone' => $data['phone'],
                    'recipient_address' => $data['address'].', '. $data['thana'].', '. $data['city'].' - '.$data['zip'].', '.$data['country'],
                    'cod_amount' => $singleProduct->order_amount,
                    'note' => 'NA'
                ]);
            // api respose
            $responseData = json_decode($response->getBody()->getContents(), true);

                // Check if the response contains the necessary data
                if (isset($responseData['status']) && $responseData['status'] == 200 && isset($responseData['consignment'])) {
                    $consignmentData = $responseData['consignment'];

                    // Update the orders table with the consignment data
                    DB::table('orders')->where('id', $id)->update([
                        'order_status' => 'out_for_delivery',
                        'delivery_type' => 'third_party_delivery',
                        'delivery_service_name' => 'Stead Fast',
                        'third_party_delivery_tracking_id' => $consignmentData['tracking_code'] ?? null,
                        'consignment_id' => $consignmentData['consignment_id'] ?? null,
                        'invoice_no' => $consignmentData['invoice'] ?? null,
                        'updated_at' => now()
                    ]);

                    return response()->json([
                        'success' => true,
                        'reload' => true // reload the page
                    ]);
                } else {
                    return response()->json([
                        'error' => true
                    ]);
                }


            }
        }

    }

    // send bulk products
    public function sendBulkProducts(Request $request)
    {
        // Retrieve the orderIds from the request
        $orderIds = $request->input('orderIds');
        $allProductData = [];

        //$invoiceId=150;
        // Fetch all product details according to id
        foreach ($orderIds as $orderId) {
            $singleProduct = DB::table('orders')->where('id', $orderId)->first();

            if ($singleProduct && $singleProduct->shipping_address_data) {
                $singleProductInfo = $singleProduct->shipping_address_data;

                // Decode JSON data
                $data = json_decode($singleProductInfo, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {
                $singleProductData =[
                    'invoice' => $singleProduct->id,
                    'recipient_name' => $data['contact_person_name'],
                    'recipient_phone' => $data['phone'],
                    'recipient_address' => $data['address'].', '. $data['thana'].', '. $data['city'].' - '.$data['zip'].', '.$data['country'],
                    'cod_amount' => $singleProduct->order_amount,
                    'note' => 'NA'
                ] ;
                $allProductData[] = $singleProductData;
                }
            }

           // $invoiceId+=1;
        }

        // Convert all product data to JSON
        $allProductDataJson = json_encode($allProductData);

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json'
            ])->post($this->baseUrl.'/create_order/bulk-order', [
                'data' => $allProductDataJson,
            ]);

        // api respose
        $data= json_decode($response->getBody()->getContents(), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if ($data['status'] === 200) {
                // Access the data array
                $items = $data['data'];
                foreach ($items as $item) {
                    // Update the orders table with the consignment data
                    DB::table('orders')->where('id', $item['invoice'])->update([
                        'order_status' => 'out_for_delivery',
                        'delivery_type' => 'third_party_delivery',
                        'delivery_service_name' => 'Stead Fast',
                        'third_party_delivery_tracking_id' => $item['tracking_code'] ?? null,
                        'consignment_id' => $item['consignment_id'] ?? null,
                        'invoice_no' => $item['invoice'] ?? null,
                        'updated_at' => now()
                    ]);
                }
            }else {
                // Handle error or status code
                return response()->json([
                    'error' => true
                ]);
            }

        } else {
            // Handle error
            return response()->json([
                'error' => true
            ]);
        }
         // Return a success response
        return response()->json([
            'success' => true,
            'reload' => true // reload the page
        ]);
    }



    public function get_data_from_api(): JsonResponse
    {
        $apiEndPoint='/get_balance';
        $response = $this->steadFastApiGetData($apiEndPoint);
        return response()->json($response);
    }

    // check delivery status
    public function steadfastDeliveryStatus($trackingID){
        $apiEndPoint='/status_by_trackingcode/'.$trackingID;
        $response = $this->steadFastApiGetData($apiEndPoint);
        return response()->json($response);
    }

}

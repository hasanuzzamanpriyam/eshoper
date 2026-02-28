<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\OrderManager;
use App\Model\BusinessSetting;
use App\Model\DeliveryMan;
use App\Model\DeliveryManTransaction;
use App\Model\DeliverymanWallet;
use App\Model\DeliveryZipCode;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Model\Seller;
use App\Traits\CommonTrait;
use App\Model\ShippingAddress;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Ramsey\Uuid\Uuid;
use function App\CPU\translate;
use App\CPU\CustomerManager;
use App\CPU\Convert;
use App\Exports\OrderExport;
use App\Model\Customer;
use App\Models\User as ModelsUser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Rap2hpoutre\FastExcel\FastExcel;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class CustomerReputationController extends Controller
{
    //customer reputation check
    public function aacheckCustomerReputationStatus($phone){
        $singleProductData =[
                'invoice' => 'dd',
                'recipient_name' => 'contact_person_name',
                'recipient_phone' => 'phone',
                'recipient_address' => 'city',
                'cod_amount' => 'order_amount',
                'note' => 'NA'
            ] ;

            $singleProductData=json_encode($singleProductData);
            var_dump($singleProductData);

        //     return response()->json([
        //     'success' => $singleProductData
        // ]);
    }


    public function checkCustomerReputationStatus($phone){


        // Define the base URL and API key
        $baseUrl = 'https://dash.hoorin.com/api/courier/search.php?apiKey=';
        $apiKey = '7278b0fb46f17ca5a45499'; // Replace with your actual API key
        $endpoint = '&searchTerm=' . $phone;

        // Full URL
        $url = $baseUrl . $apiKey . $endpoint;

        // Make the GET request to the API
        $response = Http::get($url);

        // Check if the request was successful
        if ($response->successful()) {
            // Return the response data as JSON
            return response()->json($response->json());
        } else {
            // Return an error if the request failed
            return response()->json(['error' => 'Failed to fetch data from the API'], $response->status());
        }

    }
}

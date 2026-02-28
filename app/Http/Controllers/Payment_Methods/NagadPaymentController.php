<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Http\Controllers\Controller;
use App\Model\PaymentRequest;
use App\User;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NagadPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private $merchant_id;
    private $merchant_number;
    private $public_key;
    private $private_key;
    private $base_url;
    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('nagad', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
            $this->base_url = 'http://api.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs';
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
            $this->base_url = 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs';
        }

        if ($config) {
            $this->merchant_id = $this->config_values->merchant_id ?? '';
            $this->merchant_number = $this->config_values->merchant_number ?? '';
            $this->public_key = $this->config_values->public_key ?? '';
            $this->private_key = $this->config_values->private_key ?? '';
        }

        $this->payment = $payment;
        $this->user = $user;
    }

    private function encryptData($data)
    {
        $public_key = trim($this->public_key);
        // Remove any existing headers/footers
        $public_key = str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n", "\r"], '', $public_key);
        // Add proper headers
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($public_key, 64, "\n") . "-----END PUBLIC KEY-----";

        $key = openssl_pkey_get_public($public_key);
        if (!$key) {
            throw new \Exception('Invalid public key');
        }

        openssl_public_encrypt($data, $encrypted, $key);
        return base64_encode($encrypted);
    }

    private function signData($data)
    {
        $private_key = trim($this->private_key);
        // Remove any existing headers/footers
        $private_key = str_replace(['-----BEGIN RSA PRIVATE KEY-----', '-----END RSA PRIVATE KEY-----', "\n", "\r"], '', $private_key);
        // Add proper headers
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . chunk_split($private_key, 64, "\n") . "-----END RSA PRIVATE KEY-----";

        $key = openssl_pkey_get_private($private_key);
        if (!$key) {
            throw new \Exception('Invalid private key');
        }

        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    private function decryptData($data)
    {
        $private_key = trim($this->private_key);
        // Remove any existing headers/footers
        $private_key = str_replace(['-----BEGIN RSA PRIVATE KEY-----', '-----END RSA PRIVATE KEY-----', "\n", "\r"], '', $private_key);
        // Add proper headers
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . chunk_split($private_key, 64, "\n") . "-----END RSA PRIVATE KEY-----";

        $key = openssl_pkey_get_private($private_key);
        if (!$key) {
            throw new \Exception('Invalid private key for decryption');
        }

        openssl_private_decrypt(base64_decode($data), $decrypted, $key);
        return $decrypted;
    }

    public function initialize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        try {
            // Generate order ID
            $orderId = 'ORD_' . time() . '_' . Str::random(6);

            // Store in session for callback
            session()->put('nagad_payment_id', $request['payment_id']);
            session()->put('nagad_order_id', $orderId);

            // Step 1: Initialize Payment
            $dateTime = date('YmdHis');
            $sensitiveData = [
                'merchantId' => $this->merchant_id,
                'datetime' => $dateTime,
                'orderId' => $orderId,
                'challenge' => Str::random(40)
            ];

            $postData = [
                'accountNumber' => $this->merchant_number,
                'dateTime' => $dateTime,
                'sensitiveData' => $this->encryptData(json_encode($sensitiveData)),
                'signature' => $this->signData(json_encode($sensitiveData))
            ];

            $initUrl = $this->base_url . '/check-out/initialize/' . $this->merchant_id . '/' . $orderId;

            $ch = curl_init($initUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-KM-IP-V4: ' . request()->ip(),
                'X-KM-Client-Type: PC_WEB'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Log the response for debugging
            Log::info('Nagad Init Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'curl_error' => $curlError,
                'url' => $initUrl
            ]);

            if ($httpCode != 200) {
                Log::error('Nagad API Error', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'curl_error' => $curlError
                ]);
                throw new \Exception('Failed to initialize payment. HTTP Code: ' . $httpCode);
            }

            $result = json_decode($response, true);

            if (!isset($result['sensitiveData']) || !isset($result['signature'])) {
                throw new \Exception('Invalid response from Nagad');
            }

            // Decrypt response
            $plainResponse = json_decode($this->decryptData($result['sensitiveData']), true);

            // Step 2: Complete Payment
            $paymentUrl = $this->base_url . '/check-out/complete/' . $plainResponse['paymentReferenceId'];
            $callbackUrl = route('nagad.callback', ['payment_id' => $request['payment_id']]);

            $sensitivePaymentData = [
                'merchantId' => $this->merchant_id,
                'orderId' => $orderId,
                'currencyCode' => '050',
                'amount' => number_format($data->payment_amount, 2, '.', ''),
                'challenge' => $plainResponse['challenge']
            ];

            $merchantAdditionalInfo = [
                'payment_id' => $request['payment_id']
            ];

            $completeData = [
                'paymentReferenceId' => $plainResponse['paymentReferenceId'],
                'callbackUrl' => $callbackUrl,
                'merchantCallbackURL' => $callbackUrl,
                'sensitiveData' => $this->encryptData(json_encode($sensitivePaymentData)),
                'signature' => $this->signData(json_encode($sensitivePaymentData)),
                'merchantAdditionalInfo' => json_encode($merchantAdditionalInfo)
            ];

            $ch = curl_init($paymentUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($completeData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-KM-IP-V4: ' . request()->ip(),
                'X-KM-Client-Type: PC_WEB'
            ]);

            $completeResponse = curl_exec($ch);
            curl_close($ch);

            $completeResult = json_decode($completeResponse, true);

            if (isset($completeResult['callBackUrl'])) {
                return redirect()->away($completeResult['callBackUrl']);
            }

            throw new \Exception('Failed to get payment URL');
        } catch (\Exception $e) {
            Log::error('Nagad Payment Error: ' . $e->getMessage());
            return redirect()->route('payment-fail')->with('error', 'Payment initialization failed');
        }
    }

    public function callback(Request $request)
    {
        try {
            $payment_ref = $request->input('payment_ref_id');
            $status = $request->input('status');
            $order_id = $request->input('order_id');

            // Get payment_id from session or request
            $payment_id = session('nagad_payment_id') ?? $request->input('payment_id');

            if (!$payment_id) {
                throw new \Exception('Payment ID not found');
            }

            // Verify payment with Nagad
            $verifyUrl = $this->base_url . '/verify/payment/' . $payment_ref;

            $ch = curl_init($verifyUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-KM-IP-V4: ' . request()->ip(),
                'X-KM-Client-Type: PC_WEB'
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            // Check if payment is successful
            if (isset($result['status']) && strtolower($result['status']) === 'success') {
                $this->payment::where(['id' => $payment_id])->update([
                    'payment_method' => 'nagad',
                    'is_paid' => 1,
                    'transaction_id' => $payment_ref ?? 'NAGAD_' . time(),
                ]);

                $payment_data = $this->payment::where(['id' => $payment_id])->first();

                if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                    call_user_func($payment_data->success_hook, $payment_data);
                }

                // Clear session
                session()->forget(['nagad_payment_id', 'nagad_order_id']);

                return $this->payment_response($payment_data, 'success');
            }

            throw new \Exception('Payment verification failed');
        } catch (\Exception $e) {
            Log::error('Nagad Callback Error: ' . $e->getMessage());

            $payment_id = session('nagad_payment_id') ?? $request->input('payment_id');
            $payment_data = $payment_id ? $this->payment::where(['id' => $payment_id])->first() : null;

            if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
                call_user_func($payment_data->failure_hook, $payment_data);
            }

            session()->forget(['nagad_payment_id', 'nagad_order_id']);

            return $this->payment_response($payment_data, 'fail');
        }
    }
}

<?php

namespace App\Http\Controllers\Payment_Methods;

use App\CPU\CartManager;
use App\Model\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Foundation\Application;

class PayTicPaymentController extends Controller
{
    use Processor;

    private $api_key;
    private $base_url;
    private $callback_base_url;
    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paytic', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $values = json_decode($config->test_values);
        }

        if ($config) {
            $this->api_key = $values->api_key;
            $this->base_url = $values->base_url ?? 'https://pay.tic.bd/api';
            $this->callback_base_url = $values->callback_url ?? url('/');
        }
        $this->payment = $payment;
        $this->user = $user;
    }

    public function index(Request $request)
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

        $payment_amount = $data['payment_amount'];
        $payer_information = json_decode($data['payer_information']);

        $post_data = [
            'full_name' => $payer_information->name,
            'email_address' => $payer_information->email ?? 'example@example.com',
            'mobile_number' => $payer_information->phone ?? '01700000000',
            'amount' => (string)round($payment_amount, 2),
            'currency' => $data['currency_code'],
            'metadata' => json_encode(['payment_id' => $data['id']]),
            'return_url' => $this->callback_base_url . '/payment/paytic/callback?payment_id=' . $data['id'],
            'webhook_url' => $this->callback_base_url . '/payment/paytic/webhook',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/checkout/redirect');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'MHS-PIPRAPAY-API-KEY: ' . $this->api_key,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($code != 200 || !empty($curl_error)) {
            return back();
        }

        $result = json_decode($response, true);

        if (isset($result['pp_url']) && $result['pp_url'] != '') {
            $additional = json_decode($data->additional_data, true) ?? [];
            $additional['cart_group_ids'] = CartManager::get_cart_group_ids();

            $this->payment::where(['id' => $data['id']])->update([
                'transaction_id' => $result['pp_id'],
                'additional_data' => json_encode($additional),
            ]);
            return redirect($result['pp_url']);
        }

        return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
    }

    public function callback(Request $request)
    {
        $payment_id = $request['payment_id'];
        $payment_data = $this->payment::where(['id' => $payment_id])->first();

        if (!$payment_data) {
            info('PayTic callback: payment_data not found for id: ' . ($payment_id ?? 'null'));
            return redirect()->route('payment-fail');
        }

        if ($payment_data->is_paid == 1) {
            return $this->payment_response($payment_data, 'success');
        }

        $pp_id = $request['pp_id'] ?? $payment_data->transaction_id;

        if (!$pp_id) {
            info('PayTic callback: pp_id not found for payment: ' . $payment_id);
            if (function_exists($payment_data->failure_hook)) {
                call_user_func($payment_data->failure_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'fail');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/verify-payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pp_id' => $pp_id]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'MHS-PIPRAPAY-API-KEY: ' . $this->api_key,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        info('PayTic verify: code=' . $code . ' body=' . ($response ?: 'empty') . ' error=' . ($curl_error ?: 'none'));

        if ($code == 200 && empty($curl_error)) {
            $result = json_decode($response, true);

            $status = isset($result['status']) ? strtolower($result['status']) : '';

            if (in_array($status, ['completed', 'successful', 'success'])) {
                $this->payment::where(['id' => $payment_id])->update([
                    'payment_method' => 'paytic',
                    'is_paid' => 1,
                    'transaction_id' => $result['transaction_id'] ?? $pp_id,
                ]);

                $data = $this->payment::where(['id' => $payment_id])->first();
                if (isset($data) && function_exists($data->success_hook)) {
                    call_user_func($data->success_hook, $data);
                }
                return $this->payment_response($data, 'success');
            }

            info('PayTic verify: unexpected status. Full: ' . json_encode($result));
        }

        if (function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    public function webhook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data) {
            $status = $data['status'] ?? 'unknown';
            $pp_id = $data['pp_id'] ?? null;
            $transaction_id = $data['transaction_id'] ?? null;

            $metadata = $data['metadata'] ?? null;
            $payment_id = null;

            if ($metadata && is_array($metadata) && isset($metadata['payment_id'])) {
                $payment_id = $metadata['payment_id'];
            }

            if ($payment_id && in_array($status, ['completed', 'successful', 'success'])) {
                $existing = $this->payment::where(['id' => $payment_id])->first();
                if ($existing && $existing->is_paid == 1) {
                    return response()->json(['status' => 'ok']);
                }

                $this->payment::where(['id' => $payment_id])->update([
                    'payment_method' => 'paytic',
                    'is_paid' => 1,
                    'transaction_id' => $transaction_id ?? $pp_id,
                ]);

                $payment_data = $this->payment::where(['id' => $payment_id])->first();
                if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                    call_user_func($payment_data->success_hook, $payment_data);
                }
            }

            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
    }
}

<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Http\Controllers\Controller;
use App\Model\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NagadPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private $merchant_id;
    private $merchant_number;
    private $public_key;
    private $private_key;
    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('nagad', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
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

        // Check if we have real credentials or dummy/empty ones
        $has_real_credentials = !empty($this->merchant_id) &&
            !empty($this->merchant_number) &&
            !empty($this->public_key) &&
            !empty($this->private_key);

        // TEST MODE: If using dummy/empty credentials, simulate payment flow
        if (!$has_real_credentials) {
            // Redirect to callback with test success
            $callback_url = route('nagad.callback', [
                'payment_id' => $request['payment_id'],
                'status' => 'success',
                'payment_ref_id' => 'TEST_NAGAD_' . time()
            ]);

            return redirect()->away($callback_url);
        }

        // REAL NAGAD API IMPLEMENTATION GOES HERE
        // When you have real credentials from Nagad:
        // 1. Generate timestamp and random data
        // 2. Create payment request payload
        // 3. Sign with private key
        // 4. Call Nagad Initialize API
        // 5. Redirect to Nagad payment URL

        // For now, still use test mode even with credentials
        $callback_url = route('nagad.callback', [
            'payment_id' => $request['payment_id'],
            'status' => 'success',
            'payment_ref_id' => 'NAGAD_' . Str::random(10)
        ]);

        return redirect()->away($callback_url);
    }

    public function callback(Request $request)
    {
        $payment_id = $request['payment_id'];
        $status = $request['status'] ?? 'fail';
        $payment_ref_id = $request['payment_ref_id'] ?? null;

        // Handle successful payment
        if ($status === 'success') {
            $this->payment::where(['id' => $payment_id])->update([
                'payment_method' => 'nagad',
                'is_paid' => 1,
                'transaction_id' => $payment_ref_id ?? 'NAGAD_TEST_' . time(),
            ]);

            $data = $this->payment::where(['id' => $payment_id])->first();

            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }

            return $this->payment_response($data, 'success');
        }

        // Handle payment failure
        $payment_data = $this->payment::where(['id' => $payment_id])->first();

        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        return $this->payment_response($payment_data, 'fail');
    }
}

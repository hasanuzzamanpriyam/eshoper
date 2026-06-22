<?php

use App\CPU\BackEndHelper;
use App\CPU\CartManager;
use App\CPU\CustomerManager;
use App\CPU\OrderManager;
use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\PendingCheckout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

if (!function_exists('digital_payment_success')) {
    function digital_payment_success($payment_data)
    {
        if (isset($payment_data) && $payment_data['is_paid'] == 1) {
            $unique_id = OrderManager::gen_unique_id();
            $order_ids = [];
            $additional_data = json_decode($payment_data['additional_data']);

            $stored_group_ids = isset($additional_data->cart_group_ids) && is_array($additional_data->cart_group_ids)
                ? $additional_data->cart_group_ids
                : null;

            $data = [];
            if ($stored_group_ids) {
                $cart_group_ids = $stored_group_ids;
                // Use stored address/customer data from additional_data if available
                // (needed for webhooks that run without browser session)
                if (isset($additional_data->customer_id)) {
                    $name_guest_reg = '';
                    $phone_guest_reg = '';
                    $email_guest_reg = '';

                    if ($additional_data->is_guest ?? 0) {
                        $address = \App\Model\ShippingAddress::where(['customer_id' => $additional_data->customer_id, 'is_guest' => 1])
                            ->latest()->first();
                        if ($address) {
                            $name_guest_reg = $address->contact_person_name ?? '';
                            $phone_guest_reg = $address->phone ?? '';
                            $email_guest_reg = $address->email ?? '';
                        }
                    }

                    $data['request'] = [
                        'customer_id' => $additional_data->customer_id,
                        'is_guest' => $additional_data->is_guest ?? 0,
                        'guest_id' => ($additional_data->is_guest ?? 0) ? ($additional_data->guest_id ?? $additional_data->customer_id) : null,
                        'name_guest_reg' => $name_guest_reg,
                        'phone_guest_reg' => $phone_guest_reg,
                        'email_guest_reg' => $email_guest_reg,
                        'order_note' => $additional_data->order_note ?? null,
                        'coupon_code' => $additional_data->coupon_code ?? null,
                        'coupon_discount' => $additional_data->coupon_discount ?? null,
                        'address_id' => $additional_data->address_id ?? null,
                        'billing_address_id' => $additional_data->billing_address_id ?? null,
                        'payment_request_from' => $additional_data->payment_request_from ?? 'web',
                    ];
                }
            } elseif (isset($additional_data->payment_request_from) && in_array($additional_data->payment_request_from, ['app', 'react'])) {
                $data += [
                    'request' => [
                        'customer_id' => $additional_data->customer_id,
                        'is_guest' => $additional_data->is_guest ?? 0,
                        'guest_id' => $additional_data->is_guest ? $additional_data->customer_id : null,
                        'order_note' => $additional_data->order_note,
                        'coupon_code' => $additional_data->coupon_code ?? null,
                        'coupon_discount' => $additional_data->coupon_discount ?? null,
                        'address_id' => $additional_data->address_id ?? null,
                        'billing_address_id' => $additional_data->billing_address_id ?? null,
                        'payment_request_from' => $additional_data->payment_request_from,
                    ],
                ];

                if ($additional_data->is_guest) {
                    $cart_group_ids = Cart::where(['customer_id' => $additional_data->customer_id, 'is_guest' => 1])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
                } else {
                    $cart_group_ids = Cart::where(['customer_id' =>  $additional_data->customer_id, 'is_guest' => '0'])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
                }
            } else {
                $cart_group_ids = CartManager::get_cart_group_ids();
            }
            session()->put('payment_mode', isset($additional_data->payment_mode) ? $additional_data->payment_mode : 'web');

            foreach ($cart_group_ids as $group_id) {
                $data += [
                    'payment_method' => $payment_data['payment_method'],
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                    'transaction_ref' => $payment_data['transaction_id'],
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id
                ];
                $order_id = OrderManager::generate_order($data);
                unset($data['payment_method']);
                unset($data['cart_group_id']);
                array_push($order_ids, $order_id);
            }

            // Store order IDs in session for the order complete page
            session()->put('order_ids', $order_ids);

            if ($stored_group_ids) {
                CartShipping::whereIn('cart_group_id', $stored_group_ids)->delete();
                Cart::whereIn('cart_group_id', $stored_group_ids)->delete();
            }
            if (isset($additional_data->payment_request_from) && in_array($additional_data->payment_request_from, ['app', 'react'])) {
                CartManager::cart_clean_for_api_digital_payment($data);
            } else {
                CartManager::cart_clean();
            }

            try {
                $pendingCheckout = null;
                if (isset($additional_data->payment_request_from) && in_array($additional_data->payment_request_from, ['app', 'react'])) {
                    if ($additional_data->is_guest) {
                        $pendingCheckout = PendingCheckout::where('guest_id', $additional_data->customer_id)
                            ->where('status', 'pending')->latest()->first();
                    } else {
                        $pendingCheckout = PendingCheckout::where('customer_id', $additional_data->customer_id)
                            ->where('status', 'pending')->latest()->first();
                    }
                } else {
                    $firstCart = Cart::whereIn('cart_group_id', $cart_group_ids)->first();
                    if ($firstCart) {
                        if ($firstCart->is_guest) {
                            $pendingCheckout = PendingCheckout::where('guest_id', $firstCart->customer_id)
                                ->where('status', 'pending')->latest()->first();
                        } else {
                            $pendingCheckout = PendingCheckout::where('customer_id', $firstCart->customer_id)
                                ->where('status', 'pending')->latest()->first();
                        }
                    }
                }

                if ($pendingCheckout) {
                    $pendingCheckout->update([
                        'status' => 'paid',
                        'order_id' => $order_ids[0] ?? null,
                        'paid_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                info('Pending checkout update failed: ' . $e->getMessage());
            }
        }
    }
}

if (!function_exists('digital_payment_fail')) {
    function digital_payment_fail($payment_data) {}
}

// Add Fund To Wallet - Success
if (!function_exists('add_fund_to_wallet_success')) {
    function add_fund_to_wallet_success($payment_data)
    {
        if (isset($payment_data) && $payment_data['is_paid'] == 1) {
            $additional_data = json_decode($payment_data['additional_data']);
            session()->put('payment_mode', isset($additional_data->payment_mode) ? $additional_data->payment_mode : 'web');

            $wallet_transaction = CustomerManager::create_wallet_transaction($payment_data['payer_id'], floatval($payment_data['payment_amount']), 'add_fund', 'add_funds_to_wallet', $payment_data);

            if ($wallet_transaction) {
                try {
                    Mail::to($wallet_transaction->user->email)->send(new \App\Mail\AddFundToWallet($wallet_transaction));
                } catch (\Exception $ex) {
                    info($ex);
                }
            }
        }
    }
}

// Add Fund To Wallet - Fail
if (!function_exists('add_fund_to_wallet_fail')) {
    function add_fund_to_wallet_fail($payment_data) {}
}

if (!function_exists('config_settings')) {
    function config_settings($key, $settings_type)
    {
        try {
            $config = DB::table('addon_settings')->where('key_name', $key)
                ->where('settings_type', $settings_type)->first();
        } catch (Exception $exception) {
            return null;
        }
        return (isset($config)) ? $config : null;
    }
}

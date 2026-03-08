<?php

/**
 * Test script to verify cart calculations are consistent
 * This script helps identify caching issues in cart calculations
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\CPU\CartManager;
use App\CPU\Helpers;

// Boot Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cart Calculation Test ===\n\n";

// Test 1: Check if CartManager methods are consistent
echo "Test 1: Checking CartManager consistency\n";
echo "----------------------------------------\n";

$cart = CartManager::get_cart();
$shipping_cost = CartManager::get_shipping_cost();
$grand_total = CartManager::cart_grand_total();
$order_wise_shipping_discount = CartManager::order_wise_shipping_discount();
$get_shipping_cost_saved_for_free_delivery = CartManager::get_shipping_cost_saved_for_free_delivery();

echo "Cart items count: " . $cart->count() . "\n";
echo "Shipping cost: " . $shipping_cost . "\n";
echo "Grand total: " . $grand_total . "\n";
echo "Order wise shipping discount: " . $order_wise_shipping_discount . "\n";
echo "Shipping cost saved for free delivery: " . $get_shipping_cost_saved_for_free_delivery . "\n\n";

// Test 2: Manual calculation vs CartManager calculation
echo "Test 2: Manual calculation vs CartManager\n";
echo "----------------------------------------\n";

$manual_sub_total = 0;
$manual_total_tax = 0;
$manual_total_discount = 0;

foreach ($cart as $cartItem) {
    $manual_sub_total += $cartItem['price'] * $cartItem['quantity'];
    $manual_total_tax += $cartItem['tax_model'] == 'exclude' ? ($cartItem['tax'] * $cartItem['quantity']) : 0;
    $manual_total_discount += $cartItem['discount'] * $cartItem['quantity'];
}

$manual_total_shipping_cost = session()->missing('coupon_type') || session('coupon_type') != 'free_delivery'
    ? ($shipping_cost - $get_shipping_cost_saved_for_free_delivery)
    : $shipping_cost;

$manual_grand_total = $manual_sub_total + $manual_total_tax + $manual_total_shipping_cost;

echo "Manual sub total: " . $manual_sub_total . "\n";
echo "Manual total tax: " . $manual_total_tax . "\n";
echo "Manual total discount: " . $manual_total_discount . "\n";
echo "Manual shipping cost: " . $manual_total_shipping_cost . "\n";
echo "Manual grand total: " . $manual_grand_total . "\n";
echo "CartManager grand total: " . $grand_total . "\n";

if (abs($manual_grand_total - $grand_total) < 0.01) {
    echo "✓ Manual and CartManager calculations match!\n\n";
} else {
    echo "✗ Manual and CartManager calculations DO NOT match!\n";
    echo "Difference: " . ($manual_grand_total - $grand_total) . "\n\n";
}

// Test 3: Cache key generation test
echo "Test 3: Cache key generation\n";
echo "----------------------------------------\n";

$coupon_dis = session()->has('coupon_discount') ? session('coupon_discount') : 0;
$cart_cache_key = 'cart_summary_' . md5(serialize($cart->pluck('id')->toArray()) . $coupon_dis . session('coupon_type') . $shipping_cost);

echo "Cache key: " . $cart_cache_key . "\n";
echo "Cache key length: " . strlen($cart_cache_key) . "\n\n";

echo "=== Test Complete ===\n";
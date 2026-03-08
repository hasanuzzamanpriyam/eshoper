<?php

namespace App\Services;

use App\Model\Coupon;
use App\Model\Order;
use Exception;
use Carbon\Carbon;
use App\CPU\Helpers;

class CouponService
{
    /**
     * Apply coupon to a cart and calculate discount.
     *
     * @param string $code
     * @param iterable $cartItems
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function apply(string $code, $cartItems, int $userId)
    {
        // 1. Fetch Coupon
        $coupon = Coupon::where(['code' => $code])
            ->where('status', 1)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('expire_date', '>=', Carbon::today())
            ->first();

        if (!$coupon) {
            throw new Exception("Invalid or expired coupon code.");
        }

        // 2. Validate Usage Limit (Total)
        $totalUsage = Order::where('coupon_code', $code)->count();
        if ($totalUsage >= $coupon->limit) {
            throw new Exception("Coupon usage limit exceeded.");
        }

        // 3. Validate Usage Limit (Per User)
        // Assuming 'limit' is total limit. If there is a per-user limit, check it here.
        // For 'first_order', check if user has previous orders.
        if ($coupon->coupon_type == 'first_order') {
            $userOrders = Order::where('customer_id', $userId)->count();
            if ($userOrders > 0) {
                throw new Exception("This coupon is only for first-time orders.");
            }
        }

        // 4. Calculate Eligible Amount
        $eligibleTotal = 0;
        $eligibleItems = [];

        foreach ($cartItems as $item) {
            $isEligible = false;

            // Admin Query: seller_id is NULL or 0 -> GLOBAL COUPON (Applies to all products)
            if (is_null($coupon->seller_id) || $coupon->seller_id == 0) {
                $isEligible = true;
            }
            // Seller Query: seller_id matches item owner -> SPECIFIC COUPON
            elseif ($item->seller_id == $coupon->seller_id && $item->seller_is == 'seller') {
                $isEligible = true;
            }

            if ($isEligible) {
                $eligibleTotal += $item->price * $item->quantity;
                $eligibleItems[] = $item;
            }
        }

        if ($eligibleTotal == 0) {
            throw new Exception("This coupon is not applicable to any items in your cart.");
        }

        // 5. Validate Minimum Purchase
        if ($eligibleTotal < $coupon->min_purchase) {
            throw new Exception("Minimum purchase of " . Helpers::currency_converter($coupon->min_purchase) . " required.");
        }

        // 6. Calculate Discount
        $discountAmount = 0;
        if ($coupon->discount_type == 'percentage') {
            $discountAmount = ($eligibleTotal * $coupon->discount) / 100;
            if ($discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        } else {
            $discountAmount = $coupon->discount;
        }

        return [
            'coupon' => $coupon,
            'total_discount' => $discountAmount,
            'discount_bearer' => $coupon->coupon_bearer, // 'inhouse' or 'seller'
            'eligible_items' => $eligibleItems,
            'seller_id' => $coupon->seller_id
        ];
    }
}

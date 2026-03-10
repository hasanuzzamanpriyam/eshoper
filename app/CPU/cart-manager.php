<?php

namespace App\CPU;

use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\Color;
use App\Model\Product;
use App\Model\Shop;
use Illuminate\Support\Str;
use App\Model\ShippingType;
use App\Model\CategoryShippingCost;
use App\Model\DeliveryCharge;
use App\Model\ShippingAddress;
use App\Models\Districtname;
use Illuminate\Support\Facades\Log;

class CartManager
{
    public static function cart_to_db($request = null)
    {
        $user = Helpers::get_customer($request);
        if (session()->has('guest_id') || $request->guest_id) {
            $guest_id = session('guest_id') ?? $request->guest_id;
            $carts = Cart::where(['is_guest' => 1, 'customer_id' => $guest_id])->get();
            foreach ($carts as $cart) {
                $db_cart = Cart::where([
                    'customer_id' => $user->id,
                    'seller_id' => $cart['seller_id'],
                    'seller_is' => $cart['seller_is']
                ])->first();

                $cart->cart_group_id = isset($db_cart) ? $db_cart['cart_group_id'] : str_replace('guest', $user->id, $cart['cart_group_id']);
                $cart->customer_id = $user->id;
                $cart->is_guest = 0;
                $cart->save();
            }
        }
    }

    public static function get_cart($group_id = null)
    {
        $user = Helpers::get_customer();
        if ($user == 'offline') {
            if ($group_id == null) {
                return Cart::whereIn('cart_group_id', CartManager::get_cart_group_ids())->get();
            } else {
                return Cart::where('cart_group_id', $group_id)->get();
            }
        }

        if ($group_id == null) {
            $cart = Cart::whereIn('cart_group_id', CartManager::get_cart_group_ids())->get();
        } else {
            $cart = Cart::where('cart_group_id', $group_id)->get();
        }

        return $cart;
    }

    public static function get_cart_for_api($request, $group_id = null)
    {
        if ($group_id == null) {
            $cart = Cart::whereIn('cart_group_id', CartManager::get_cart_group_ids($request))->get();
        } else {
            $cart = Cart::where('cart_group_id', $group_id)->get();
        }

        return $cart;
    }

    public static function get_cart_group_ids($request = null)
    {
        $user = Helpers::get_customer($request);

        if ($user == 'offline') {
            $cart_ids = Cart::where(['customer_id' => session('guest_id') ?? ($request->guest_id ?? 0), 'is_guest' => 1])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
        } else {
            $cart_ids = Cart::where(['customer_id' => $user->id, 'is_guest' => '0'])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
        }

        return $cart_ids;
    }

    public static function get_shipping_cost($group_id = null)
    {
        $cost = 0;
        if ($group_id == null) {
            $cart_shipping_cost = Cart::where(['product_type' => 'physical'])->whereIn('cart_group_id', CartManager::get_cart_group_ids())->sum('shipping_cost');
            $order_wise_shipping_cost = CartShipping::whereHas('cart', function ($query) {
                $query->where(['product_type' => 'physical']);
            })
                ->whereIn('cart_group_id', CartManager::get_cart_group_ids())->sum('shipping_cost');
            $cost = $order_wise_shipping_cost + $cart_shipping_cost;

            $cost += self::get_district_based_delivery_charge();
        } else {
            $data = CartShipping::whereHas('cart', function ($query) {
                $query->where(['product_type' => 'physical']);
            })->where('cart_group_id', $group_id)->first();

            $order_wise_shipping_cost = isset($data) ? $data->shipping_cost : 0;
            $cart_shipping_cost = Cart::where(['cart_group_id' => $group_id, 'product_type' => 'physical'])->sum('shipping_cost');
            $cost = $order_wise_shipping_cost + $cart_shipping_cost;

            $cost += self::get_district_based_delivery_charge($group_id);
        }
        return $cost;
    }

    /**
     * Calculate delivery charge based on customer's district for products with shipping_cost = 0
     */
    public static function get_district_based_delivery_charge($group_id = null)
    {
        $additional_cost = 0;

        $address_id = session('address_id');
        $district = '';

        if (!$address_id) {
            // If address_id is not in session (happens during checkout-details before saving),
            // check if a district was selected via AJAX and stored in session
            $district = session('selected_district');
            if (empty($district) || strtolower($district) == 'select district' || trim($district) == '') {
                return 0;
            }
        } else {
            $shipping_address = ShippingAddress::find($address_id);
            if (!$shipping_address) {
                // Fallback to session district if address record not found
                $district = session('selected_district');
                if (empty($district) || strtolower($district) == 'select district' || trim($district) == '') {
                    return 0;
                }
            } else {
                $district = $shipping_address->city;
            }
        }

        if (empty($district) || strtolower($district) == 'select district' || trim($district) == '') {
            return 0;
        }
        $delivery_charge = DeliveryCharge::first();
        if (!$delivery_charge) {
            return 0;
        }

        $district_charge = 0;
        if (stripos($district, 'Rajshahi') !== false) {
            $district_charge = $delivery_charge->local_delivery_charge;
        } else {
            $district_charge = $delivery_charge->country_delivery_charge;
        }

        // Get cart items
        if ($group_id == null) {
            $cart_items = Cart::where(['product_type' => 'physical'])
                ->whereIn('cart_group_id', CartManager::get_cart_group_ids())
                ->get();
        } else {
            $cart_items = Cart::where(['cart_group_id' => $group_id, 'product_type' => 'physical'])
                ->get();
        }

        $products_without_shipping = 0;
        foreach ($cart_items as $cart_item) {
            $product = Product::find($cart_item->product_id);
            if ($product && $product->shipping_cost == 0 && $product->is_delivery_free == 0) {
                $products_without_shipping++;
            }
        }

        // Apply district charge for each product without shipping cost
        $additional_cost = $products_without_shipping * $district_charge;

        return $additional_cost;
    }

    /**
     * Calculate delivery charge based on district parameter (for AJAX real-time updates)
     * @param string $district The district name or ID
     * @param int|null $group_id The cart group ID
     * @return float The calculated delivery charge
     */
    public static function calculate_delivery_charge_by_district($district, $group_id = null)
    {
        $additional_cost = 0;

        // Check if district is provided
        if (empty($district) || strtolower($district) == 'select district' || trim($district) == '') {
            Log::warning('Empty district provided to calculate_delivery_charge_by_district');
            return 0;
        }

        // If district is numeric (ID), try to fetch the name from database
        if (is_numeric($district)) {
            $districtModel = Districtname::find($district);
            if ($districtModel) {
                $district = $districtModel->district_name_en;
                Log::info('Converted district ID to name', ['id' => $district, 'name' => $districtModel->district_name_en]);
            } else {
                Log::warning('District ID not found in database', ['id' => $district]);
                return 0;
            }
        }

        // Get delivery charges from database
        $delivery_charge = DeliveryCharge::first();
        if (!$delivery_charge) {
            Log::error('No delivery charge configuration found in database');
            return 0;
        }

        // Determine which charge to use based on district
        $district_charge = 0;
        if (stripos($district, 'Rajshahi') !== false) {
            $district_charge = $delivery_charge->local_delivery_charge;
            Log::info('Using local delivery charge for Rajshahi', ['charge' => $district_charge]);
        } else {
            $district_charge = $delivery_charge->country_delivery_charge;
            Log::info('Using country delivery charge', ['district' => $district, 'charge' => $district_charge]);
        }

        // Get cart items
        if ($group_id == null) {
            $cart_items = Cart::where(['product_type' => 'physical'])
                ->whereIn('cart_group_id', CartManager::get_cart_group_ids())
                ->get();
        } else {
            $cart_items = Cart::where(['cart_group_id' => $group_id, 'product_type' => 'physical'])
                ->get();
        }

        // Count how many products have shipping_cost = 0
        $products_without_shipping = 0;
        foreach ($cart_items as $cart_item) {
            $product = Product::find($cart_item->product_id);
            if ($product && $product->shipping_cost == 0 && $product->is_delivery_free == 0) {
                $products_without_shipping++;
            }
        }

        // Apply district charge for each product without shipping cost
        $additional_cost = $products_without_shipping * $district_charge;

        Log::info('District delivery charge calculated', [
            'district' => $district,
            'products_without_shipping' => $products_without_shipping,
            'district_charge' => $district_charge,
            'total_additional_cost' => $additional_cost
        ]);

        return $additional_cost;
    }

    public static function order_wise_shipping_discount()
    {
        return 0; // Delivery discounts are now correctly generalized in the coupon logic
    }

    public static function cart_total($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = $item['price'] * $item['quantity'];
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_total_applied_discount($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = ($item['price'] - $item['discount']) * $item['quantity'];
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_total_with_tax($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = ($item['price'] * $item['quantity']) + ($item['tax'] * $item['quantity']);
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_grand_total($cart_group_id = null)
    {
        $cart = CartManager::get_cart($cart_group_id);
        $shipping_cost = CartManager::get_shipping_cost($cart_group_id);
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $tax = $item['tax_model'] == 'include' ? 0 : $item['tax'];
                $product_subtotal = ($item['price'] * $item['quantity'])
                    + ($tax * $item['quantity'])
                    - $item['discount'] * $item['quantity'];
                $total += $product_subtotal;
            }
            $total += $shipping_cost;
        }
        return $total;
    }

    public static function api_cart_grand_total($request, $cart_group_id = null)
    {
        $cart = CartManager::get_cart_for_api($request, $cart_group_id);
        $shipping_cost = CartManager::get_shipping_cost($cart_group_id);
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $tax = $item['tax_model'] == 'include' ? 0 : $item['tax'];
                $product_subtotal = ($item['price'] * $item['quantity'])
                    + ($tax * $item['quantity'])
                    - $item['discount'] * $item['quantity'];
                $total += $product_subtotal;
            }
            $total += $shipping_cost;
        }
        return $total;
    }

    public static function cart_grand_total_without_shipping_charge($cart_group_id = null)
    {
        $cart = CartManager::get_cart($cart_group_id);
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $tax = $item['tax_model'] == 'include' ? 0 : $item['tax'];
                $product_subtotal = ($item['price'] * $item['quantity'])
                    + ($tax * $item['quantity'])
                    - $item['discount'] * $item['quantity'];
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_clean($request = null)
    {
        $cart_ids = CartManager::get_cart_group_ids($request);
        CartShipping::whereIn('cart_group_id', $cart_ids)->delete();
        Cart::whereIn('cart_group_id', $cart_ids)->delete();

        session()->forget('coupon_code');
        session()->forget('coupon_type');
        session()->forget('coupon_bearer');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');
        session()->forget('billing_address_id');
        session()->forget('order_id');
        session()->forget('cart_group_id');
        session()->forget('order_note');
    }

    public static function cart_clean_for_api_digital_payment($data)
    {
        if ($data['request']['is_guest']) {
            $cart_ids = Cart::where(['customer_id' => $data['request']['customer_id'], 'is_guest' => 1])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
        } else {
            $cart_ids = Cart::where(['customer_id' => $data['request']['customer_id'], 'is_guest' => '0'])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
        }

        CartShipping::whereIn('cart_group_id', $cart_ids)->delete();
        Cart::whereIn('cart_group_id', $cart_ids)->delete();
    }

    public static function add_to_cart($request, $from_api = false)
    {
        $str = '';
        $variations = [];
        $price = 0;

        $user = Helpers::get_customer($request);
        $product = Product::find($request->id);
        $guest_id = session('guest_id') ?? ($request->guest_id ?? 0);

        //check the color enabled or disabled for the product
        if ($request->has('color')) {
            $str = Color::where('code', $request['color'])->first()->name;
            $variations['color'] = $str;
        }

        //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
        $choices = [];
        foreach (json_decode($product->choice_options) as $key => $choice) {
            $choices[$choice->name] = $request[$choice->name];
            $variations[$choice->title] = $request[$choice->name];
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }

        if ($user == 'offline') {
            $cart = Cart::where(['product_id' => $request->id, 'customer_id' => $guest_id, 'is_guest' => 1, 'variant' => $str])->first();
            if (isset($cart) == false) {
                $cart = new Cart();
            } else {
                return [
                    'status' => 0,
                    'message' => translate('already_added!')
                ];
            }
        } else {
            $cart = Cart::where(['product_id' => $request->id, 'customer_id' => $user->id, 'is_guest' => '0', 'variant' => $str])->first();
            if (isset($cart) == false) {
                $cart = new Cart();
            } else {
                return [
                    'status' => 0,
                    'message' => translate('already_added!')
                ];
            }
        }

        $cart['color'] = $request->has('color') ? $request['color'] : null;
        $cart['product_id'] = $product->id;
        $cart['product_type'] = $product->product_type;
        $cart['choices'] = json_encode($choices);

        //chek if out of stock
        if (($product['product_type'] == 'physical') && ($product['current_stock'] < $request['quantity'])) {
            return [
                'status' => 0,
                'message' => translate('out_of_stock!')
            ];
        }

        $cart['variations'] = json_encode($variations);
        $cart['variant'] = $str;

        //Check the string and decreases quantity for the stock
        if ($str != null) {
            $count = count(json_decode($product->variation));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variation)[$i]->type == $str) {
                    $price = json_decode($product->variation)[$i]->price;
                    if (json_decode($product->variation)[$i]->qty < $request['quantity']) {
                        return [
                            'status' => 0,
                            'message' => translate('out_of_stock!')
                        ];
                    }
                }
            }
        } else {
            $price = $product->unit_price;
        }

        $tax = Helpers::tax_calculation($price, $product['tax'], 'percent');

        //generate group id
        if ($user == 'offline') {
            $cart_check = Cart::where([
                'customer_id' => $guest_id,
                'is_guest' => 1,
                'seller_id' => ($product->added_by == 'admin') ? 1 : $product->user_id,
                'seller_is' => $product->added_by
            ])->first();
        } else {
            $cart_check = Cart::where([
                'customer_id' => $user->id,
                'is_guest' => '0',
                'seller_id' => ($product->added_by == 'admin') ? 1 : $product->user_id,
                'seller_is' => $product->added_by
            ])->first();
        }

        if (isset($cart_check)) {
            $cart['cart_group_id'] = $cart_check['cart_group_id'];
        } else {
            $cart['cart_group_id'] = ($user == 'offline' ? 'guest' : $user->id) . '-' . Str::random(5) . '-' . time();
        }
        //generate group id end

        $cart['customer_id'] = ($user == 'offline' ? $guest_id : $user->id);
        $cart['is_guest'] = ($user == 'offline' ? 1 : 0);
        $cart['quantity'] = $request['quantity'];
        $cart['price'] = $price;
        $cart['tax'] = $tax;
        $cart['tax_model'] = $product->tax_model;
        $cart['slug'] = $product->slug;
        $cart['name'] = $product->name;
        $cart['discount'] = Helpers::get_product_discount($product, $price);
        $cart['thumbnail'] = $product->thumbnail;
        $cart['seller_id'] = ($product->added_by == 'admin') ? 1 : $product->user_id;
        $cart['seller_is'] = $product->added_by;
        $cart['shipping_cost'] = $product->product_type == 'physical' ? CartManager::get_shipping_cost_for_product_category_wise($product, $request['quantity']) : 0;
        if ($product->added_by == 'seller') {
            $cart['shop_info'] = Shop::where(['seller_id' => $product->user_id])->first()->name;
        } else {
            $cart['shop_info'] = Helpers::get_business_settings('company_name');
        }

        $shippingMethod = Helpers::get_business_settings('shipping_method');

        if ($shippingMethod == 'inhouse_shipping') {
            $admin_shipping = ShippingType::where('seller_id', 0)->first();
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
        } else {
            if ($product->added_by == 'admin') {
                $admin_shipping = ShippingType::where('seller_id', 0)->first();
                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
            } else {
                $seller_shipping = ShippingType::where('seller_id', $product->user_id)->first();
                $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
            }
        }
        $cart['shipping_type'] = $shipping_type;
        $cart->save();

        $cats = json_decode($product->category_ids);
        $categories = [];
        foreach ($cats as $cat) {
            $categories[] = $cat->id;
        }

        return [
            'status' => 1,
            'product_id' => $cart['product_id'],
            'item_name' => $product->name,
            'brand_id' => $product->brand_id,
            'seller_id' => $product->user_id,
            'code' => $product->code,
            'reviews_count' => $product->reviews_count,
            'stock' => $product->current_stock,
            'price' => $price,
            'quantity' => (int) $request['quantity'],
            'tax' => $product->tax,
            'discount' => $product->discount,
            'categories' => $categories,
            'message' => translate('successfully_added!')
        ];
    }

    public static function update_cart_qty($request)
    {
        $user = Helpers::get_customer($request);
        $guest_id = session('guest_id') ?? ($request->guest_id ?? 0);
        $status = 1;
        $qty = 0;
        $cart = Cart::where(['id' => $request->key, 'customer_id' => ($user == 'offline' ? $guest_id : $user->id)])->first();

        $product = Product::find($cart['product_id']);
        $count = count(json_decode($product->variation));
        if ($count) {
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variation)[$i]->type == $cart['variant']) {
                    if (json_decode($product->variation)[$i]->qty < $request->quantity) {
                        $status = 0;
                        $qty = $cart['quantity'];
                    }
                }
            }
        } else if (($product['product_type'] == 'physical') && $product['current_stock'] < $request->quantity) {
            $status = 0;
            $qty = $cart['quantity'];
        }

        if ($status) {
            $qty = $request->quantity;
            $cart['quantity'] = $request->quantity;
            $cart['shipping_cost'] = CartManager::get_shipping_cost_for_product_category_wise($product, $request->quantity);
        }

        $cart->save();

        return [
            'status' => $status,
            'qty' => $qty,
            'message' => $status == 1 ? translate('successfully_updated!') : translate('sorry_stock_is_limited')
        ];
    }

    public static function get_shipping_cost_for_product_category_wise($product, $qty)
    {
        if ($product->is_delivery_free) {
            return 0;
        }
        $shippingMethod = Helpers::get_business_settings('shipping_method');
        $cost = 0;

        if ($shippingMethod == 'inhouse_shipping') {
            $admin_shipping = ShippingType::where('seller_id', 0)->first();
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
        } else {
            if ($product->added_by == 'admin') {
                $admin_shipping = ShippingType::where('seller_id', 0)->first();
                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
            } else {
                $seller_shipping = ShippingType::where('seller_id', $product->user_id)->first();
                $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
            }
        }

        if ($shipping_type == 'category_wise') {
            $categoryID = 0;
            foreach (json_decode($product->category_ids) as $ct) {
                if ($ct->position == 1) {
                    $categoryID = $ct->id;
                }
            }

            if ($shippingMethod == 'inhouse_shipping') {
                $category_shipping_cost = CategoryShippingCost::where('seller_id', 0)->where('category_id', $categoryID)->first();
            } else {
                if ($product->added_by == 'admin') {
                    $category_shipping_cost = CategoryShippingCost::where('seller_id', 0)->where('category_id', $categoryID)->first();
                } else {
                    $category_shipping_cost = CategoryShippingCost::where('seller_id', $product->user_id)->where('category_id', $categoryID)->first();
                }
            }



            if ($category_shipping_cost->multiply_qty == 1) {
                $cost = $qty * $category_shipping_cost->cost;
            } else {
                $cost = $category_shipping_cost->cost;
            }
        } else if ($shipping_type == 'product_wise') {

            if ($product->multiply_qty == 1) {
                $cost = $qty * $product->shipping_cost;
            } else {
                $cost = $product->shipping_cost;
            }
        } else {
            $cost = 0;
        }

        return $cost;
    }

    public static function get_shipping_cost_saved_for_free_delivery($group_id = null)
    {
        $cost_saved = 0;
        if ($group_id) {
            $cart_group = Cart::where(['product_type' => 'physical'])->where('cart_group_id', $group_id)->get()->groupBy('cart_group_id');
        } else {
            $cart_group = Cart::where(['product_type' => 'physical'])->whereIn('cart_group_id', CartManager::get_cart_group_ids())->get()->groupBy('cart_group_id');
        }

        foreach ($cart_group as $cart) {
            if ($cart->count() > 0) {
                $free_delivery_check = OrderManager::free_delivery_order_amount($cart[0]->cart_group_id);
                $cost_saved += $free_delivery_check['shipping_cost_saved'];
            }
        }

        return $cost_saved;
    }

    public static function product_stock_check($carts): bool
    {
        $status = true;

        foreach ($carts as $cart) {
            $product = Product::find($cart['product_id']);
            $count = count(json_decode($product->variation));
            if ($count) {
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variation)[$i]->type == $cart['variant']) {
                        if (json_decode($product->variation)[$i]->qty < $cart->quantity) {
                            $status = false;
                        }
                    }
                }
            } else if (($product['product_type'] == 'physical') && $product['current_stock'] < $cart->quantity) {
                $status = false;
            }
        }

        return $status;
    }
}
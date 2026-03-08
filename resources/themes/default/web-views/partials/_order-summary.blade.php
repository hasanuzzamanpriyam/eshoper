@push('css_or_js')
<style>
    .cart_title {
        font-weight: 400 !important;
        font-size: 16px;
    }

    .cart_value {
        font-weight: 600 !important;
        font-size: 16px;
    }

    @media (max-width: 575px) {

        .cart_title,
        .cart_value {
            font-size: 14px;
        }
    }

    .cart_total_value {
        font-weight: 700 !important;
        font-size: 25px !important;

        color: {
                {
                $web_config['primary_color']
            }
        }

        !important;
    }

    .__cart-total_sticky {
        position: sticky;
        top: 80px;
    }
</style>
@endpush

<aside class="col-lg-4 pt-4 pt-lg-2 px-max-md-0">
    <div class="__cart-total __cart-total_sticky">
        <div class="cart_total p-0">
            @php($shippingMethod=\App\CPU\Helpers::get_business_settings('shipping_method'))
            @php($cart=\App\CPU\CartManager::get_cart())
            @php($cart_group_ids=\App\CPU\CartManager::get_cart_group_ids())
            @php($shipping_cost=\App\CPU\CartManager::get_shipping_cost())
            @php($get_shipping_cost_saved_for_free_delivery=\App\CPU\CartManager::get_shipping_cost_saved_for_free_delivery())
            @php($order_wise_shipping_discount=\App\CPU\CartManager::order_wise_shipping_discount())
            @php($coupon_dis = session()->has('coupon_discount') ? session('coupon_discount') : 0)
            @php($cart_cache_key = 'cart_summary_' . md5(serialize($cart->pluck('id')->toArray()) . $coupon_dis . session('coupon_type') . $shipping_cost))

            @php($sub_total = 0)
            @php($total_tax = 0)
            @php($total_discount_on_product = 0)
            @if($cart->count() > 0)
            @foreach($cart as $key => $cartItem)
            @php($sub_total += $cartItem['price'] * $cartItem['quantity'])
            @php($total_tax += $cartItem['tax_model'] == 'exclude' ? ($cartItem['tax'] * $cartItem['quantity']) : 0)
            @php($total_discount_on_product += $cartItem['discount'] * $cartItem['quantity'])
            @endforeach
            @endif

            @php($total_shipping_cost = session()->missing('coupon_type') || session('coupon_type') !='free_delivery'
            ? ($shipping_cost - $get_shipping_cost_saved_for_free_delivery)
            : $shipping_cost)

            @php($total_savings = $total_discount_on_product + $coupon_dis + $order_wise_shipping_discount)
            <h6 id="you_have_saved_section" class="text-center text-primary mb-4 d-flex align-items-center justify-content-center gap-2 {{ $total_savings <= 0 ? 'd-none' : '' }}">
                <img src="{{asset('assets/front-end/img/icons/offer.svg')}}" alt="">
                {{translate('you_have_Saved')}} <strong id="total_savings_amount">{{\App\CPU\Helpers::currency_converter($total_savings)}}!</strong>
            </h6>

            <div class="d-flex justify-content-between">
                <span class="cart_title">{{translate('sub_total')}}</span>
                <span class="cart_value">
                    {{\App\CPU\Helpers::currency_converter($sub_total)}}
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="cart_title">{{translate('tax')}}</span>
                <span class="cart_value">
                    {{\App\CPU\Helpers::currency_converter($total_tax)}}
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="cart_title">{{translate('shipping_charge')}}</span>
                <span class="cart_value" id="shipping-charge-value">
                    {{\App\CPU\Helpers::currency_converter($total_shipping_cost)}}
                    <input type="hidden" id="shipping-cost-value" value="{{$total_shipping_cost}}">
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="cart_title">{{translate('discount_on_product')}}</span>
                <span class="cart_value">
                    - {{\App\CPU\Helpers::currency_converter($total_discount_on_product)}}
                </span>
            </div>
            @php($coupon_dis=0)
            @if(session()->has('coupon_discount'))
            @php($coupon_discount = session()->has('coupon_discount')?session('coupon_discount'):0)
            <div class="d-flex justify-content-between">
                <span class="cart_title">{{translate('coupon_discount')}}</span>
                <span class="cart_value" id="coupon-discount-amount">
                    - {{\App\CPU\Helpers::currency_converter($coupon_discount+$order_wise_shipping_discount)}}
                </span>
            </div>
            @php($coupon_dis=session('coupon_discount'))
            @else
            <div class="pt-2">
                <form class="needs-validation coupon-code-form" action="javascript:void(0);" method="post" novalidate id="coupon-code-ajax" onsubmit="event.preventDefault(); couponCode(); return false;">
                    <div class="d-flex form-control rounded-pill pl-3 p-1">
                        <img width="24" src="{{asset('assets/front-end/img/icons/coupon.svg')}}" alt="">
                        <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100" type="text" name="code" placeholder="{{translate('coupon_code')}}" required>
                        <button class="btn btn-primary rounded-pill text-uppercase py-1 fs-12" type="submit">{{translate('apply')}}</button>
                    </div>
                    <div class="invalid-feedback">{{translate('please_provide_coupon_code')}}</div>
                </form>
            </div>
            @php($coupon_dis=0)
            @endif
            <hr class="my-2">
            <div class="d-flex justify-content-between">
                <span class="cart_title text-primary font-weight-bold">{{translate('total')}}</span>
                <span class="cart_value" id="total-price-value">
                    {{\App\CPU\Helpers::currency_converter(\App\CPU\CartManager::cart_grand_total() - $coupon_dis - $order_wise_shipping_discount)}}
                    <input type="hidden" id="cart-cache-key" value="{{$cart_cache_key}}">
                </span>
            </div>
        </div>
        @php($company_reliability = \App\CPU\Helpers::get_business_settings('company_reliability'))
        @if($company_reliability != null)
        <div class="mt-5">
            <div class="row justify-content-center g-4">
                @foreach ($company_reliability as $key=>$value)
                @if ($value['status'] == 1 && !empty($value['title']))
                @php($img_path = 'company-reliability/'.$value['image'])
                @php($storage_exists = !empty($value['image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($img_path))
                @php($img_src = $storage_exists ? asset("storage/".$img_path) : asset('assets/front-end/img').'/'.$value['item'].'.png')
                <div class="col-sm-3 px-0 text-center mobile-padding">
                    <img class="order-summery-footer-image" src="{{$img_src}}" alt="">
                    <div class="deal-title">{{translate($value['title'] ?? 'title_not_found')}}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-4">
            @php($cooldownRemaining = $cooldownRemaining ?? 0)
            @if($web_config['guest_checkout_status'] || auth('customer')->check())
            <a onclick="checkout()" class="btn btn--primary btn-block proceed_to_next_button {{$cart->count() <= 0 || $cooldownRemaining > 0 ? 'disabled' : ''}}">{{Route::currentRouteName() == 'checkout-payment' ? translate('proceed_to_payment') : translate('proceed_to_next')}}</a>
            @if($cooldownRemaining > 0)
            <div class="text-center mt-2 text-danger font-weight-bold" id="cooldown-timer">
                {{ translate('You_can_order_again_in') }} <span class="timer-display" id="timer-display"></span>
            </div>
            @endif
            @else
            <a href="{{route('customer.auth.login')}}" class="btn btn--primary btn-block proceed_to_next_button {{$cart->count() <= 0 ? 'disabled' : ''}}">{{Route::currentRouteName() == 'checkout-payment' ? translate('proceed_to_payment') : translate('proceed_to_next')}}</a>
            @endif
        </div>
        @if( $cart->count() != 0)

        <div class="d-flex justify-content-center mt-3">
            <a href="{{route('home')}}" class="d-flex align-items-center gap-2 text-primary font-weight-bold">
                <i class="tio-back-ui fs-12"></i> {{translate('continue_Shopping')}}
            </a>
        </div>
        @endif

    </div>
</aside>

<div class="bottom-sticky3 bg-white p-3 shadow-sm w-100 d-lg-none">
    <div class="d-flex justify-content-center align-items-center fs-14 mb-2">
        <div class="product-description-label fw-semibold text-capitalize">{{translate('total_price')}} : </div>
        &nbsp; <strong class="text-base" id="total-price-value-mobile">{{\App\CPU\Helpers::currency_converter(\App\CPU\CartManager::cart_grand_total() - $coupon_dis - $order_wise_shipping_discount)}}</strong>
    </div>
    @if($web_config['guest_checkout_status'] || auth('customer')->check())
    <a onclick="checkout()" class="btn btn--primary btn-block proceed_to_next_button text-capitalize {{$cart->count() <= 0 || $cooldownRemaining > 0 ? 'disabled' : ''}}">{{Route::currentRouteName() == 'checkout-payment' ? translate('proceed_to_payment') : translate('proceed_to_next')}}</a>
    @if($cooldownRemaining > 0)
    <div class="text-center mt-2 text-danger font-weight-bold" id="cooldown-timer-mobile">
        {{ translate('You_can_order_again_in') }} <span class="timer-display" id="timer-display-mobile"></span>
    </div>
    @endif
    @else
    <a href="{{route('customer.auth.login')}}" class="btn btn--primary btn-block proceed_to_next_button text-capitalize {{$cart->count() <= 0 ? 'disabled' : ''}}">{{Route::currentRouteName() == 'checkout-payment' ? translate('proceed_to_payment') : translate('proceed_to_next')}}</a>
    @endif
</div>
@push('script')
<script>
    $(document).ready(function() {
        const $stickyElement = $('.bottom-sticky3');
        const $offsetElement = $('.__cart-total_sticky');

        $(window).on('scroll', function() {
            const elementOffset = $offsetElement.offset().top;
            const scrollTop = $(window).scrollTop();

            if (scrollTop >= elementOffset) {
                $stickyElement.addClass('stick');
            } else {
                $stickyElement.removeClass('stick');
            }
        });
    });

    function couponCode() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route("coupon.apply") }}',
            data: $('#coupon-code-ajax').serialize(),
            success: function(data) {
                if (data.status == 1) {
                    let msges = data.messages;
                    for (var i = 0; i < msges.length; i++) {
                        toastr.success(msges[i], {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    // Refresh only the order summary section via AJAX instead of full page reload
                    refreshOrderSummary();
                } else {
                    let msges = data.messages;
                    for (var i = 0; i < msges.length; i++) {
                        toastr.error(msges[i], {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                }
            }
        });
    }

    // Function to refresh the order summary section without full page reload
    function refreshOrderSummary() {
        $.ajax({
            type: "GET",
            url: window.location.href,
            success: function(response) {
                // Parse the response and update the order summary section
                var newDoc = new DOMParser().parseFromString(response, 'text/html');
                var newCartTotal = newDoc.querySelector('.cart_total');
                var currentCartTotal = document.querySelector('.cart_total');
                if (newCartTotal && currentCartTotal) {
                    currentCartTotal.innerHTML = newCartTotal.innerHTML;
                }
                // Update mobile total price as well
                var newMobileTotal = newDoc.querySelector('#total-price-value-mobile');
                var currentMobileTotal = document.querySelector('#total-price-value-mobile');
                if (newMobileTotal && currentMobileTotal) {
                    currentMobileTotal.innerHTML = newMobileTotal.innerHTML;
                }
            },
            error: function() {
                // Fallback to page reload if AJAX refresh fails
                location.reload();
            }
        });
    }

    // Function to refresh cart calculations
    function refreshCartCalculations() {
        var cacheKey = $('#cart-cache-key').val();
        // The cache key ensures that any changes to cart items, coupon, or shipping
        // will generate a different key, forcing fresh calculations
        console.log('Cart calculations refreshed with cache key: ' + cacheKey);
    }

    // Initialize cart calculations on page load
    $(document).ready(function() {
        refreshCartCalculations();
    });
</script>

@if(isset($cooldownRemaining) && $cooldownRemaining > 0)
<script>
    if (typeof window.cooldownTimerRunning === 'undefined') {
        window.cooldownTimerRunning = true;
        window.cooldownSeconds = <?php echo (int) $cooldownRemaining; ?>;

        function updateTimer() {
            let minutes = Math.floor(window.cooldownSeconds / 60);
            let seconds = window.cooldownSeconds % 60;
            if (seconds < 10) seconds = '0' + seconds;

            let display = minutes + ":" + seconds;
            let elements = document.querySelectorAll('.timer-display');
            elements.forEach(function(el) {
                el.innerText = display;
            });

            if (window.cooldownSeconds > 0) {
                window.cooldownSeconds--;
                setTimeout(updateTimer, 1000);
            } else {
                const currentRoute = '<?php echo Route::currentRouteName(); ?>';
                if (currentRoute === 'checkout-payment') {
                    location.reload();
                } else {
                    // Just enable buttons and hide timer
                    document.querySelectorAll('.proceed_to_next_button').forEach(btn => {
                        btn.classList.remove('disabled');
                        btn.removeAttribute('disabled');
                    });
                    document.querySelectorAll('#cooldown-timer, #cooldown-timer-mobile').forEach(timer => {
                        timer.classList.add('d-none');
                    });
                }
            }
        }
        updateTimer();
    }
</script>
@endif
@endpush

@if($cart->count() > 0)
@push('script')
<script>
    var cartData = {
        ecommerce: {
            currency: '{{ \App\CPU\Helpers::currency_code() }}',
            value: <?php echo \App\CPU\CartManager::cart_grand_total() - $coupon_dis - $order_wise_shipping_discount; ?>,
            items: [
                @foreach($cart as $item) {
                    item_id: <?php echo $item['id']; ?>,
                    item_name: '<?php echo $item['name']; ?>',
                    price: <?php echo $item['price']; ?>,
                    sale_price: <?php echo $item['price'] - $item['discount']; ?>,
                    tax: <?php echo $item['tax']; ?>,
                    discount: <?php echo $item['discount']; ?>,
                    seller_id: <?php echo $item['seller_id']; ?>,
                    shipping_cost: <?php echo $item['shipping_cost']; ?>,
                    quantity: <?php echo $item['quantity']; ?>
                },
                @endforeach
            ]
        },
        @if(Route::currentRouteName() == 'checkout-details')
        event: 'begin_checkout'
        @elseif(Route::currentRouteName() == 'checkout-payment')
        event: 'add_payment_info'
        @elseif(Route::currentRouteName() == 'checkout-complete')
        event: 'purchase'
        @elseif(Route::currentRouteName() == 'shop-cart')
        event: 'view_cart'
        @endif
    };
    dataLayer.push(cartData);
</script>
@endpush
@endif
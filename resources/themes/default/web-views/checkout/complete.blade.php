@extends('layouts.front-end.app')

@section('title', translate('order_Complete'))

@push('css_or_js')
    <style>

        .spanTr {
            color: {{$web_config['primary_color']}};
        }

        .amount {
            color: {{$web_config['primary_color']}};
        }

        @media (max-width: 600px) {
            .orderId {
                margin- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 91px;
            }
        }
        .table-wrapper-mobile td {
            text-align: right;
        }
        @media (max-width: 768px) {
            .table-wrapper-desktop {
                display: none;
            }
        }
        @media (min-width: 769px) {
            .table-wrapper-mobile {
                display: none;
            }
        }
        /*  */
    </style>
@endpush

@section('content')
    <div class="container mt-5 mb-5 rtl __inline-53"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card">
                    @if(auth('customer')->check() || session('guest_id'))
                        <div class="card-body">
                            <div class="mb-3">
                                <center>
                                    <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                                </center>
                            </div>

                            <h6 class="font-black fw-bold text-center">{{ translate('order_placed_successfully')}}</h6>

                            @if (isset($order_ids) && count($order_ids) > 0)
                                @php
                                    $summary = [];
                                @endphp
                                @foreach ($order_ids as $key => $order)
                                    @php
                                        $orderDetails = \App\CPU\OrderManager::track_order($order);
                                        $orderData = \App\CPU\OrderManager::get_order_details($order);
                                        $order_summary = \App\CPU\OrderManager::order_summary($orderDetails);
                                    @endphp

                                    {{-- Ensure $orderDetails exists --}}
                                    @if($orderDetails)
                                        @php
                                            // Initialize or append to summary values
                                            $summary['invoice_no'] = $summary['invoice_no'] ?? [];
                                            $summary['invoice_no'][] = $orderDetails->id;

                                            $summary['order_date'] = date('F d, Y', strtotime($orderDetails->created_at));

                                            $summary['total_price'] = $summary['total_price'] ?? 0;
                                            $summary['total_price'] += $orderDetails->order_amount - $orderDetails->shipping_cost;

                                            $summary['total_shipping'] = $summary['total_shipping'] ?? 0;
                                            $summary['total_shipping'] += $orderDetails->shipping_cost;

                                            $summary['payment_method'] = $summary['payment_method'] ?? [];
                                            $payment_method = translate($orderDetails->payment_method);
                                            if (!in_array($payment_method, $summary['payment_method'])){
                                                $summary['payment_method'][] = $payment_method;
                                            }
                                        @endphp
                                    @endif
                                @endforeach
                                <h6 class="font-black fw-bold mt-4 text-center">{{ translate('order_details') }}</h6>
                                <div class="table-wrapper table-wrapper-desktop table-responsive" style="max-width: 800px; margin: 0 auto;">
                                    <table class="table table-hover table-thead-bordered table-nowrap table-align-middle card-table">
                                        <thead>
                                            <tr>
                                                <th><strong>{{translate('order_id')}}</strong></th>
                                                <th><strong>{{translate('date')}}</strong></th>
                                                <th><strong>{{translate('line_total')}}</strong></th>
                                                <th><strong>{{translate('delivery_charge')}}</strong></th>
                                                <th><strong>{{translate('total')}}</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ implode(', ', $summary['invoice_no'])}}</td>
                                                <td>{{ $summary['order_date'] }}</td>
                                                <td>{{ $summary['total_price'] }} BDT</td>
                                                <td>{{ $summary['total_shipping'] }} BDT</td>
                                                <td>{{ $summary['total_price'] + $summary['total_shipping'] }} BDT</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('payment_Method')}}</strong></th>
                                                <td colspan="4">{{ implode(', ', $summary['payment_method'])}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-wrapper table-wrapper-mobile table-responsive" style="max-width: 800px; margin: 0 auto;">
                                    <table class="table table-hover table-thead-bordered table-nowrap table-align-middle card-table">
                                        <tbody>
                                            <tr>
                                                <th><strong>{{translate('order_id')}}</strong></th>
                                                <td>{{ implode(', ', $summary['invoice_no'])}}</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('date')}}</strong></th>
                                                <td>{{ $summary['order_date'] }}</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('line_total')}}</strong></th>
                                                <td>{{ $summary['total_price'] }} BDT</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('delivery_charge')}}</strong></th>
                                                <td>{{ $summary['total_shipping'] }} BDT</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('total')}}</strong></th>
                                                <td>{{ $summary['total_price'] + $summary['total_shipping'] }} BDT</td>
                                            </tr>
                                            <tr>
                                                <th><strong>{{translate('payment_Method')}}</strong></th>
                                                <td>{{ implode(', ', $summary['payment_method'])}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            @else
                                <p class="text-center fs-12">{{ translate('your_order_is_being_processed_and_will_be_completed.') }} {{ translate('You_will_receive_an_email_confirmation_when_your_order_is_placed.') }}</p>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12 text-center">

                                    <a href="{{ route('track-order.index') }}" class="btn btn-secondary mb-3 text-center">
                                        {{ translate('track_Order')}}
                                    </a>
                                    <a href="{{route('home')}}" class="btn btn--primary mb-3 text-center">{{ translate('continue_shopping') }}</a>

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
	var customerInfo = {
		customer_shipping_name: '{{ $orderDetails->shipping_address_data->contact_person_name }}',
		customer_shipping_address: '{{ $orderDetails->shipping_address_data->address }}',
		customer_shipping_city: '{{ $orderDetails->shipping_address_data->city }}',
		customer_shipping_country: '{{ $orderDetails->shipping_address_data->country }}',
		customer_shipping_zip: '{{ $orderDetails->shipping_address_data->zip }}',
		customer_shipping_phone: '{{ $orderDetails->shipping_address_data->phone }}',
		customer_billing_name: '{{ $orderDetails->billing_address_data->contact_person_name }}',
		customer_billing_address: '{{ $orderDetails->billing_address_data->address }}',
		customer_billing_city: '{{ $orderDetails->billing_address_data->city }}',
		customer_billing_country: '{{ $orderDetails->billing_address_data->country }}',
		customer_billing_zip: '{{ $orderDetails->billing_address_data->zip }}',
		customer_billing_phone: '{{ $orderDetails->billing_address_data->phone }}'
	}

	dataLayer.push(customerInfo);

	var orderInfo = {
		ecommerce: {
			transaction_id: '{{$order}}',
			currency: 'BDT',
			value: {{$order_summary['subtotal']}} - {{$order_summary['total_discount_on_product']}},
			discount: {{$order_summary['total_discount_on_product']}},
			tax: {{$order_summary['total_tax']}},
			shipping: {{$order_summary['total_shipping_cost']}},
			items: [
				@foreach ($orderData as $item)
                    @php
                        $productDetails = json_decode($item->product_details, true);
                        $cats = [];

                        if (isset($productDetails['category_ids']) && !empty($productDetails['category_ids'])) {
                            $cats = json_decode($productDetails['category_ids'], true);
                        }
                    @endphp

                    {
                        item_name: '{{ $productDetails['name'] }}',
                        item_id: {{ $productDetails['id'] }},
                        current_stock: {{ $productDetails['current_stock'] }},
                        brand_id: '{{ $productDetails['brand_id'] }}',
                        price: {{ $item['price'] }},
                        quantity: {{ $item->qty }},
                        unit: '{{ $productDetails['unit'] }}',
                        seller_id: '{{ $item->seller_id }}',
                        categories: [
                            @foreach ($cats as $cat)
                                "{{ $cat['id'] }}",
                            @endforeach
                        ]
                    },
                @endforeach
			]
		},
		event: 'purchase'
	};

	dataLayer.push(orderInfo);
</script>
@endpush

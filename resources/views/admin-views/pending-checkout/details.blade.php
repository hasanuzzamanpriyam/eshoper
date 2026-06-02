@extends('layouts.back-end.app')

@section('title', translate('pending_Checkout_Details'))

@push('css_or_js')
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{asset('assets/back-end/img/all-orders.png')}}" alt="">
            {{translate('pending_Checkout_Details')}}
        </h2>
    </div>
    <!-- End Page Title -->

    <div class="row gy-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-10 justify-content-between mb-4">
                        <div class="d-flex flex-column gap-10">
                            <h4 class="text-capitalize">{{translate('checkout_ID')}} #{{$checkout->id}}</h4>
                            <div class="">
                                {{date('d M Y, h:i A', strtotime($checkout->created_at))}}
                            </div>
                        </div>
                        <div class="text-sm-right">
                            <div class="d-flex flex-wrap gap-10 justify-content-end">
                                <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="title-color">{{translate('status')}}: </span>
                                    @if($checkout->status == 'pending')
                                        <span class="badge badge-soft-info font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('pending')}}</span>
                                    @elseif($checkout->status == 'paid')
                                        <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('paid')}}</span>
                                    @elseif($checkout->status == 'abandoned')
                                        <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('abandoned')}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($checkout->cart_items)
                    <div class="table-responsive datatable-custom">
                        <table class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('item_details')}}</th>
                                    <th>{{translate('variation')}}</th>
                                    <th>{{translate('price')}}</th>
                                    <th>{{translate('subtotal')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkout->cart_items as $i => $item)
                                <tr>
                                    <td>{{$i + 1}}</td>
                                    <td>
                                        <div class="media align-items-center gap-10">
                                            @if(!empty($item['product_image']))
                                                <img class="avatar avatar-60 rounded" src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$item['product_image']}}"
                                                    onerror="this.src='{{asset('assets/back-end/img/160x160/img2.jpg')}}'" alt="">
                                            @endif
                                            <div>
                                                <h6 class="title-color mb-0">{{$item['product_name'] ?? 'N/A'}}</h6>
                                                <div><strong>{{translate('qty')}} :</strong> {{$item['quantity']}}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$item['variation'] ?? translate('N/A')}}</td>
                                    <td>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item['price']))}}
                                    </td>
                                    <td>
                                        {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item['price'] * $item['quantity']))}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <hr />
                    <div class="row justify-content-md-end mb-3">
                        <div class="col-md-9 col-lg-8">
                            <dl class="row gy-1 text-sm-right">
                                <dt class="col-5"><strong>{{translate('total_Amount')}}</strong></dt>
                                <dd class="col-6 title-color">
                                    <strong>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($checkout->total_amount))}}</strong>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-body text-capitalize d-flex flex-column gap-4">
                    <div class="d-flex flex-column align-items-center gap-2">
                        <h4 class="mb-0 text-center">{{translate('checkout_Status')}}</h4>
                    </div>

                    <ul class="list-unstyled list-unstyled-py-4 mb-0">
                        <li>
                            <div class="d-flex justify-content-between align-items-center gap-10">
                                <span class="title-color">{{translate('status')}}</span>
                                @if($checkout->status == 'pending')
                                    <span class="badge badge-soft-info">{{translate('pending')}}</span>
                                @elseif($checkout->status == 'paid')
                                    <span class="badge badge-soft-success">{{translate('paid')}}</span>
                                @elseif($checkout->status == 'abandoned')
                                    <span class="badge badge-soft-danger">{{translate('abandoned')}}</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <div class="d-flex justify-content-between align-items-center gap-10 mt-2">
                                <span class="title-color">{{translate('created_At')}}</span>
                                <span class="title-color"><strong>{{date('d M Y, h:i A', strtotime($checkout->created_at))}}</strong></span>
                            </div>
                        </li>
                        @if($checkout->paid_at)
                        <li>
                            <div class="d-flex justify-content-between align-items-center gap-10 mt-2">
                                <span class="title-color">{{translate('paid_At')}}</span>
                                <span class="title-color"><strong>{{date('d M Y, h:i A', strtotime($checkout->paid_at))}}</strong></span>
                            </div>
                        </li>
                        @endif
                        @if($checkout->order)
                        <li>
                            <div class="d-flex justify-content-between align-items-center gap-10 mt-2">
                                <span class="title-color">{{translate('order_ID')}}</span>
                                <a href="{{route('admin.orders.details', $checkout->order_id)}}" class="title-color hover-c1">
                                    <strong>{{$checkout->order->id}}</strong>
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                        <h4 class="d-flex gap-2">
                            <img src="{{asset('assets/back-end/img/seller-information.png')}}" alt="">
                            {{translate('customer_information')}}
                        </h4>
                    </div>
                    <div class="media flex-wrap gap-3">
                        <div class="media-body d-flex flex-column gap-1">
                            <span class="title-color"><strong>{{$checkout->contact_person_name}}</strong></span>
                            <span class="title-color break-all">
                                <a href="tel:{{$checkout->phone}}" class="title-color hover-c1">{{$checkout->phone}}</a>
                            </span>
                            @if($checkout->email)
                            <span class="title-color break-all">{{$checkout->email}}</span>
                            @endif
                            <div class="mt-2">
                                @if($checkout->customer_type == 'registered')
                                    <span class="badge badge-soft-primary">{{translate('registered')}}</span>
                                @else
                                    <span class="badge badge-soft-secondary">{{translate('guest')}}</span>
                                @endif
                            </div>
                            @if($checkout->customer)
                            <div class="mt-1">
                                <span class="title-color">{{translate('customer_ID')}}: {{$checkout->customer->id}}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                        <h4 class="d-flex gap-2">
                            <img src="{{asset('assets/back-end/img/seller-information.png')}}" alt="">
                            {{translate('shipping_Address')}}
                        </h4>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-start gap-2">
                            <img src="{{asset('assets/back-end/img/location.png')}}" alt="">
                            {{$checkout->shipping_address}}
                        </div>
                        @if($checkout->city)
                        <div>
                            <span>{{translate('city')}} :</span>
                            <strong>{{$checkout->city}}</strong>
                        </div>
                        @endif
                        @if($checkout->thana)
                        <div>
                            <span>{{translate('thana')}} :</span>
                            <strong>{{$checkout->thana}}</strong>
                        </div>
                        @endif
                        @if($checkout->zip)
                        <div>
                            <span>{{translate('zip')}} :</span>
                            <strong>{{$checkout->zip}}</strong>
                        </div>
                        @endif
                        @if($checkout->country)
                        <div>
                            <span>{{translate('country')}} :</span>
                            <strong>{{$checkout->country}}</strong>
                        </div>
                        @endif
                        @if($checkout->order_comment)
                        <div style="padding-bottom: 4px;">
                            <span>{{translate('order_comment')}} :</span>
                            <strong>{{$checkout->order_comment}}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($checkout->billing_address)
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                        <h4 class="d-flex gap-2">
                            <img src="{{asset('assets/back-end/img/seller-information.png')}}" alt="">
                            {{translate('billing_Address')}}
                        </h4>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-start gap-2">
                            <img src="{{asset('assets/back-end/img/location.png')}}" alt="">
                            {{$checkout->billing_address}}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

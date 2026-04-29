@extends('layouts.front-end.app')

@section('title', translate('just_for_you') . ' ' . translate('products'))

@push('css_or_js')
    <meta property="og:image" content="{{asset('storage/company')}}/{{$web_config['web_logo']}}"/>
    <meta property="og:title" content="{{ translate('just_for_you') }} {{ translate('products') }}"/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description" content="{{ translate('discover_products_handpicked_just_for_you') }}">

    <meta property="twitter:card" content="{{asset('storage/company')}}/{{$web_config['web_logo']}}"/>
    <meta property="twitter:title" content="{{ translate('just_for_you') }} {{ translate('products') }}"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description" content="{{ translate('discover_products_handpicked_just_for_you') }}">

    <style>
        .page-item.active .page-link {
            background-color: {{$web_config['primary_color']}} !important;
        }
    </style>
@endpush

@section('content')

@php($decimal_point_settings = \App\CPU\Helpers::get_business_settings('decimal_point_settings'))
    <!-- Page Title-->
    <div class="container py-3">

        @if($banner)
<div class="mb-3 border border-2" style="width: 100%;">
    <a href="{{ $banner->url }}">
        <img class="radius-5"
             style="width: 100%; height: 200px; object-fit: fill;"
             onerror="this.src='{{asset('assets/front-end/img/placeholder.png')}}'"
             src="{{asset('storage/banner')}}/{{$banner['photo']}}"
             alt="">
    </a>
</div>
    @endif

        <div class="search-page-header">
            <div>
                <h5 class="font-semibold mb-1">Products For You</h5>
                <div class="view-page-item-count">{{$products->total()}} {{translate('items_found')}}</div>
            </div>
            <div class="d-none d-md-block">
                <div class="sorting-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                        <path d="M11.6667 7.80078L14.1667 5.30078L16.6667 7.80078" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.91675 4.46875H4.58341C4.3533 4.46875 4.16675 4.6553 4.16675 4.88542V8.21875C4.16675 8.44887 4.3533 8.63542 4.58341 8.63542H7.91675C8.14687 8.63542 8.33341 8.44887 8.33341 8.21875V4.88542C8.33341 4.6553 8.14687 4.46875 7.91675 4.46875Z" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.91675 11.9688H4.58341C4.3533 11.9688 4.16675 12.1553 4.16675 12.3854V15.7188C4.16675 15.9489 4.3533 16.1354 4.58341 16.1354H7.91675C8.14687 16.1354 8.33341 15.9489 8.33341 15.7188V12.3854C8.33341 12.1553 8.14687 11.9688 7.91675 11.9688Z" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.1667 5.30078V15.3008" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <label class="for-shoting" for="sorting">
                        <span>{{translate('sort_by')}}</span>
                    </label>
                    <select onchange="filter(this.value)">
                        <option value="latest" {{ isset($data['sort_by']) && $data['sort_by'] == 'latest' ? 'selected' : '' }}>{{translate('latest')}}</option>
                        <option value="low-high" {{ isset($data['sort_by']) && $data['sort_by'] == 'low-high' ? 'selected' : '' }}>{{translate('low_to_High_Price')}} </option>
                        <option value="high-low" {{ isset($data['sort_by']) && $data['sort_by'] == 'high-low' ? 'selected' : '' }}>{{translate('High_to_Low_Price')}}</option>
                        <option value="a-z" {{ isset($data['sort_by']) && $data['sort_by'] == 'a-z' ? 'selected' : '' }}>{{translate('A_to_Z_Order')}}</option>
                        <option value="z-a" {{ isset($data['sort_by']) && $data['sort_by'] == 'z-a' ? 'selected' : '' }}>{{translate('Z_to_A_Order')}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 rtl">
        <div class="row">
            <!-- Content -->
            <section class="col-12">
                @if (count($products) > 0)
                    <div class="row" id="ajax-products">
                        @include('web-views.products._ajax-products',['products'=>$products,'decimal_point_settings'=>$decimal_point_settings])
                    </div>
                @else
                    <div class="text-center pt-5 text-capitalize">
                        <img src="{{asset('assets/front-end/img/icons/product.svg')}}" alt="">
                        <h5>{{translate('no_product_found')}}!</h5>
                        <p class="text-center text-muted">{{translate('sorry_no_data_found_related_to_your_search')}}</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function filter(value) {
            $.get({
                url: '{{url('/')}}/just-for-you',
                data: {
                    sort_by: value
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    $('#ajax-products').html(response.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }
    </script>
@endpush

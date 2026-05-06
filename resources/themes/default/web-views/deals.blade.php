@extends('layouts.front-end.app')

@section('title', translate('flash_Deal_Products'))

@push('css_or_js')
    <meta property="og:image" content="{{asset('storage/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="og:title" content="Deals of {{$web_config['name']->value}} "/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">

    <meta property="twitter:card" content="{{asset('storage/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="twitter:title" content="Deals of {{$web_config['name']->value}}"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
    <style>
        .countdown-background{
            background: {{$web_config['primary_color']}};
        }
        .cz-countdown-minutes {
            border: .5px solid {{$web_config['primary_color']}} !important;
        }
        .cz-countdown-seconds {
            border: .5px solid {{$web_config['primary_color']}} !important;
        }
        .cz-countdown-value {
            color: white !important;
            font-weight: bold !important;
        }
        .cz-countdown-text {
            color: {{$web_config['primary_color']}} !important;
            font-weight: bold !important;
        }
        .flash_deal_product_details .flash-product-price {
            color: {{$web_config['primary_color']}};
        }
        .__flash-deals-bg {
            background: {{$web_config['primary_color']}}1A;
            padding: 0px 20px;
        }
        @media (max-width: 768px) {
            .__flash-deals-bg {
                padding: 15px;
            }
            .flash_deal_title {
                font-size: 20px !important;
            }
        }
    </style>
@endpush

@section('content')
@php($decimal_point_settings = \App\CPU\Helpers::get_business_settings('decimal_point_settings'))
<div class="__inline-59 pt-md-3">
    @if(file_exists('storage/deal/'.$deal['banner']))
        @php($deal_banner = asset('storage/deal/'.$deal['banner']))
    @else
        @php($deal_banner = asset('assets/front-end/img/flash-deals.png'))
    @endif
    <div class="container md-4 mt-3 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
         <div style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
             <div class="w-100">
                 <img src="{{$deal_banner}}" class="w-100 h-auto" style="max-height: 400px; object-fit: cover;" onerror="this.src='{{asset('assets/front-end/img/flash-deals.png')}}'">
             </div>
             <div class="__flash-deals-bg">
                 <div class="row g-3 justify-content-between align-items-center">
                     <div class="col-lg-5 col-md-6 text-center {{Session::get('direction') === "rtl" ? 'text-sm-right' : 'text-sm-left'}}">
                         <div class="flash_deal_title text-uppercase font-weight-bold"
                         style="font-size: 28px; line-height: 1.2; color: {{$web_config['primary_color']}};">
                        {{$web_config['flash_deals']->title}}
                    </div>
                         <div class="mt-1 opacity-75" style="color: {{$web_config['primary_color']}};">
                            {{translate('hurry_Up')}} ! {{translate('the_offer_is_limited')}}. {{translate('grab_while_it_lasts')}}
                         </div>
                     </div>
                     <div class="col-lg-5 col-md-6">
                         <div>
                             <div class="text-center">
                                 <div class="d-inline-block">
                                     <span class="cz-countdown d-flex justify-content-center align-items-center"
                                         data-countdown="{{$web_config['flash_deals']?date('m/d/Y',strtotime($web_config['flash_deals']['end_date'])):''}} 23:59:00"
                                         >
                                         <span class="cz-countdown-days d-flex flex-column p-2 mx-1 rounded" style="background-color: {{$web_config['primary_color']}}; padding: 0px 11px !important;">
                                             <span class="cz-countdown-value font-weight-bold text-white"></span>
                                             <span class="cz-countdown-text" style="font-size: 10px;">{{ translate('days')}}</span>
                                         </span>
                                         <span class="cz-countdown-value font-weight-bold mx-1" style="color: {{$web_config['primary_color']}} !important;">:</span>
                                         <span class="cz-countdown-hours d-flex flex-column p-2 mx-1 rounded" style="background-color: {{$web_config['primary_color']}}; padding: 0px 11px !important;">
                                             <span class="cz-countdown-value font-weight-bold text-white"></span>
                                             <span class="cz-countdown-text" style="font-size: 10px;">{{ translate('hrs')}}</span>
                                         </span>
                                         <span class="cz-countdown-value font-weight-bold mx-1" style="color: {{$web_config['primary_color']}} !important;">:</span>
                                         <span class="cz-countdown-minutes d-flex flex-column p-2 mx-1 rounded" style="background-color: {{$web_config['primary_color']}}; padding: 0px 11px !important;">
                                             <span class="cz-countdown-value font-weight-bold text-white"></span>
                                             <span class="cz-countdown-text" style="font-size: 10px;">{{ translate('min')}}</span>
                                         </span>
                                         <span class="cz-countdown-value font-weight-bold mx-1" style="color: {{$web_config['primary_color']}} !important;">:</span>
                                         <span class="cz-countdown-seconds d-flex flex-column p-2 mx-1 rounded" style="background-color: {{$web_config['primary_color']}}; padding: 0px 11px !important;">
                                             <span class="cz-countdown-value font-weight-bold text-white"></span>
                                             <span class="cz-countdown-text" style="font-size: 10px;">{{ translate('sec')}}</span>
                                         </span>
                                     </span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
    </div>
    <!-- Toolbar-->

    <!-- Products grid-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <section class="col-lg-12">
                <div class="row g-3 mt-2">
                    @if($discountPrice)
                        @foreach($deal->products as $dp)
                            @if (isset($dp->product))
                                <div class="col--xl-2 col-sm-4 col-lg-3 col-6">
                                    @include('web-views.partials._single-product',['product'=>$dp->product,'decimal_point_settings'=>$decimal_point_settings])
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        /*--flash deal Progressbar --*/
        function update_flash_deal_progress_bar(){
            const current_time_stamp = new Date().getTime();
            const start_date = new Date('{{$web_config['flash_deals']['start_date'] ?? ''}}').getTime();
            const countdownElement = document.querySelector('.cz-countdown');
            const get_end_time = countdownElement.getAttribute('data-countdown');
            const end_time = new Date(get_end_time).getTime();
            let time_progress = ((current_time_stamp - start_date) / (end_time - start_date))*100;
            const progress_bar = document.querySelector('.flash-deal-progress-bar');
            progress_bar.style.width = time_progress + '%';
        }
        update_flash_deal_progress_bar();
        setInterval(update_flash_deal_progress_bar, 10000);
        /*-- end flash deal Progressbar --*/
    </script>
@endpush




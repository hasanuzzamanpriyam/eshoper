<section class="overflow-hidden">
    <div class="container px-0 px-md-3">
        <div class="flash-deals-wrapper {{Session::get('direction') === 'rtl' ? 'rtl' : ''}}" style="background: {{$web_config['primary_color']}}1A !important; border-radius: 10px;">
<style>
    .flash-deal-timer-custom {
        padding: 0 !important;
    }
    .view-all-btn-custom {
        color: {{$web_config['primary_color']}}!important;
    }
    @media (min-width: 768px) {
        .flash-deal-timer-custom {
            margin: -16px !important;
        }
        .view-all-btn-custom {
            margin-top: -4px!important;
        }
    }
    @media (max-width: 767px) {
        .flash-deal-timer-custom {
            margin: 0 !important;
        }
        .view-all-btn-custom {
            margin-top: 0 !important;
        }
    }
</style>

            <!-- Top Section: Title, Timer, View All Button -->
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-3 gap-3">
                <!-- Title and Subtitle -->
                <div class="text-start">
                    <div class="flash-deal-text" style="color: {{$web_config['primary_color']}};">
                        <div>
                            <span class="fw-bold fs-3">{{$web_config['flash_deals']->title}}</span>
                        </div>
                        <small>{{translate('hurry_Up')}}! {{translate('the_offer_is_limited')}}. {{translate('grab_while_it_lasts')}}</small>
                    </div>
                </div>

                <!-- Timer Without Background, Custom Styling -->
                    <span class="cz-countdown d-flex justify-content-center justify-content-md-start align-items-center flash-deal-countdown mt-0 pt-0 flash-deal-timer-custom"
                          data-countdown="{{$web_config['flash_deals']?date('m/d/Y',strtotime($web_config['flash_deals']['end_date'])):''}} 23:59:00">
                        <span class="cz-countdown-days" style="border: none !important; padding: 0 !important;">
                            <span class="cz-countdown-value" style="background-color: {{$web_config['primary_color']}}; color: white; border-radius: 5px; width: 48px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"></span>
                            <span class="cz-countdown-text" style="color: {{$web_config['primary_color']}}; font-weight: bold;">{{ translate('days')}}</span>
                        </span>
                        <span class="cz-countdown-value px-1" style="color: {{$web_config['primary_color']}};">:</span>
                        <span class="cz-countdown-hours" style="border: none !important; padding: 0 !important;">
                            <span class="cz-countdown-value" style="background-color: {{$web_config['primary_color']}}; color: white; border-radius: 5px; width: 48px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"></span>
                            <span class="cz-countdown-text" style="color: {{$web_config['primary_color']}}; font-weight: bold;">{{ translate('hrs')}}</span>
                        </span>
                        <span class="cz-countdown-value px-1" style="color: {{$web_config['primary_color']}};">:</span>
                        <span class="cz-countdown-minutes" style="border: none !important; padding: 0 !important;">
                            <span class="cz-countdown-value" style="background-color: {{$web_config['primary_color']}}; color: white; border-radius: 5px; width: 48px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"></span>
                            <span class="cz-countdown-text" style="color: {{$web_config['primary_color']}}; font-weight: bold;">{{ translate('min')}}</span>
                        </span>
                        <span class="cz-countdown-value px-1" style="color: {{$web_config['primary_color']}};">:</span>
                        <span class="cz-countdown-seconds" style="border: none !important; padding: 0 !important;">
                            <span class="cz-countdown-value" style="background-color: {{$web_config['primary_color']}}; color: white; border-radius: 5px; width: 48px; height: 40px; display: inline-flex; align-items: center; justify-content: center;"></span>
                            <span class="cz-countdown-text" style="color: {{$web_config['primary_color']}}; font-weight: bold;">{{ translate('sec')}}</span>
                        </span>
                    </span>

                <!-- View All Button -->
                <div class="text-center text-md-end">
                    @if (count($web_config['flash_deals']->products)>0)
                        <a class="text-capitalize view-all-text d-flex align-items-center justify-content-center justify-content-md-end gap-1 view-all-btn-custom"
                           href="{{route('flash-deals',[$web_config['flash_deals']?$web_config['flash_deals']['id']:0])}}">
                            {{ translate('view_all')}}
                            <i class="czi-arrow-{{Session::get('direction') === 'rtl' ? 'left' : 'right'}}"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Banner Section -->
            @if(file_exists('storage/deal/'.$web_config['flash_deals']->banner))
                @php($deal_banner = asset('storage/deal/'. $web_config['flash_deals']->banner))
                <div class="w-100 overflow-hidden mb-3">
                    <img src="{{$deal_banner}}" class="w-100 h-auto" style="object-fit: cover; object-position: center;" />
                </div>
            @endif

            <!-- Product List Section -->
            <div class="row g-3 mx-max-md-0">
                @php($count = 0)
                @foreach($web_config['flash_deals']->products as $key=>$deal)
                    @if ($deal->product && $count < 6)
                        @php($count++)
                        <div class="col-6 col-sm-4 col-md-2 px-max-md-0">
                            @include('web-views.partials._feature-product',['product'=>$deal->product,'decimal_point_settings'=>$decimal_point_settings])
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
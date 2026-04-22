@extends('layouts.back-end.app')

@section('title', translate('just_for_you'))

@push('css_or_js')
<style>
    .just-for-you-table .product-thumb-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #eef2f7;
    }
    .just-for-you-table .product-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .just-for-you-table .product-name {
        color: #2d3748;
        font-weight: 500;
        transition: color 0.2s ease;
        max-width: 300px;
    }
    .just-for-you-table .product-name:hover {
        color: var(--primary-color, #3b71ca);
    }
    .just-for-you-table .product-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        background: rgba(59, 113, 202, 0.08);
        color: #3b71ca;
    }
    .just-for-you-table thead th {
        font-weight: 600;
        color: #6b7a90;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #eef2f7 !important;
        padding: 1rem 1rem;
        white-space: nowrap;
    }
    .just-for-you-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
    }
    .just-for-you-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .just-for-you-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .just-for-you-table .sl-number {
        color: #a0aec0;
        font-weight: 500;
    }
    .search-input {
        color: #2d3748 !important;
        font-weight: 500;
    }
    .search-input::placeholder {
        color: #a0aec0;
    }
    .search-input:focus {
        color: #1a202c !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-3">
            <span class="icon-circle bg-soft-primary text-primary">
                <i class="tio-heart-outlined"></i>
            </span>
            {{translate('just_for_you')}}
            <span class="badge badge-soft-dark radius-50 fz-14 ml-2">{{ $products->total() }}</span>
        </h2>
        <p class="text-muted mt-1 mb-0">{{translate('manage_products_for_personalized_recommendations')}}</p>
    </div>
    <!-- End Page Title -->

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <!-- Search & Header -->
                    <div class="p-4 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <form action="{{ route('admin.product.just-for-you') }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" name="search" class="form-control form--control search-input"
                                               placeholder="{{translate('search_products')}}" aria-label="Search"
                                               value="{{ request('search') }}" autocomplete="off">
                                        <button type="submit" class="btn btn--primary px-4">{{translate('search')}}</button>
                                    </div>
                                </form>
                            </div>
                            @if(request('search'))
                            <div class="col-lg-7 text-lg-right mt-3 mt-lg-0">
                                <a href="{{ route('admin.product.just-for-you') }}" class="btn btn-ghost-secondary">
                                    <i class="tio-clear"></i> {{translate('clear_search')}}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- End Search & Header -->

                    <!-- Table -->
                    <div class="table-responsive just-for-you-table">
                        <table class="table table-hover table-borderless align-middle w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">{{translate('SL')}}</th>
                                    <th>{{translate('product')}}</th>
                                    <th>{{translate('product_type')}}</th>
                                    <th class="text-center">{{translate('is_for_you')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($products as $k=>$product)
                                <tr>
                                    <td class="ps-4">
                                        <span class="sl-number">{{ str_pad($products->firstItem() + $k, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{route('admin.product.view',[$product['id']])}}" class="d-flex align-items-center gap-3 text-decoration-none">
                                            <div class="product-thumb-wrapper">
                                                <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                                                     onerror="this.src='{{asset('assets/back-end/img/brand-logo.png')}}'" 
                                                     class="product-thumb" alt="{{ $product['name'] }}">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 product-name">
                                                    {{\Illuminate\Support\Str::limit($product['name'], 40)}}
                                                </h6>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        @if($product['product_type'] == 'physical')
                                            <span class="product-type-badge">{{translate('physical')}}</span>
                                        @else
                                            <span class="product-type-badge">{{translate('digital')}}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php($product_name = str_replace("'",'`',$product['name']))
                                        <form action="{{route('admin.product.is-for-you-status')}}" method="post" id="product_is_for_you{{$product['id']}}_form" class="product_is_for_you_form d-inline-block">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$product['id']}}">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input" id="product_is_for_you{{$product['id']}}" name="status" value="1" {{ $product['is_for_you'] == 1 ? 'checked':'' }}
                                                    onclick="toogleStatusModal(event,'product_is_for_you{{$product['id']}}',
                                                    'product-status-on.png','product-status-off.png',
                                                    '{{translate('Want_to_Remove')}} {{$product_name}} {{translate('from_the_is_for_you_section')}}',
                                                    '{{translate('Want_to_Add')}} {{$product_name}} {{translate('to_the_is_for_you_section')}}',
                                                    `<p>{{translate('if_disabled_this_product_will_be_removed_from_the_is_for_you_section')}}</p>`,
                                                    `<p>{{translate('if_enabled_this_product_will_be_shown_in_the_is_for_you_section')}}</p>`)">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="text-center py-5">
                                            <img class="mb-3" style="max-width: 160px;" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg" alt="No Data">
                                            <p class="mb-0 text-muted">{{translate('no_data_to_show')}}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Pagination -->
                    @if(count($products) > 0)
                    <div class="p-4 border-top">
                        <div class="d-flex justify-content-lg-end">
                            {{$products->links()}}
                        </div>
                    </div>
                    @endif
                    <!-- End Pagination -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $('.product_is_for_you_form').on('submit', function(event){
            event.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.is-for-you-status')}}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    toastr.success('{{translate("is_for_you_status_updated_successfully")}}');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            });
        });
    </script>
@endpush

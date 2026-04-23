@extends('layouts.back-end.app')

@section('title', translate('just_for_you'))

@push('css_or_js')
<link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet">
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
    .priority-select {
        width: 70px;
        margin: 0 auto;
        text-align: center;
        font-size: 0.875rem;
        padding: 0.35rem 0.5rem;
        color: #2d3748 !important;
        font-weight: 600;
        background-color: #fff !important;
    }
    .priority-select option {
        color: #2d3748 !important;
        font-weight: 500;
        background-color: #fff !important;
    }
    .priority-select:focus {
        color: #1a202c !important;
        background-color: #fff !important;
    }
    select.priority-select option:checked,
    select.priority-select option:selected {
        background-color: #f0f0f0 !important;
        color: #2d3748 !important;
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

    <!-- Priority Update Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 pb-0">
            <h4 class="mb-0">{{translate('update_priority')}}</h4>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <div class="form-group mb-md-0">
                        <label class="title-color text-capitalize">{{translate('select_product')}}</label>
                        <select class="js-select2-custom form-control" name="product_id" id="priority_product_id">
                            <option value="" disabled selected>{{translate('select_product')}}</option>
                            @foreach($allJustForYouProducts as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-md-0">
                        <label class="title-color text-capitalize">{{translate('priority')}}</label>
                        <select id="for_you_priority_select" class="form-control">
                            <option value="" disabled selected>{{translate('choose_priority')}}</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                            <option value="11">{{translate('not_set')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn--primary px-4 w-100" onclick="updatePriority()"><i class="tio-save-outlined mr-1"></i> {{translate('update_priority')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Priority Update Section -->

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
                                    <th class="text-center">{{translate('for_you_priority')}}</th>
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
                                        <form action="{{route('admin.product.is-for-you-status')}}" method="post" id="product_is_for_you{{$product->id}}_form" class="product_is_for_you_form d-inline-block">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$product->id}}">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input" id="product_is_for_you{{$product->id}}" name="status" value="1" {{ $product->is_for_you == 1 ? 'checked':'' }}
                                                    onclick="toogleStatusModal(event,'product_is_for_you{{$product->id}}',
                                                    'product-status-on.png','product-status-off.png',
                                                    '{{translate('Want_to_Remove')}} {{$product_name}} {{translate('from_the_is_for_you_section')}}',
                                                    '{{translate('Want_to_Add')}} {{$product_name}} {{translate('to_the_is_for_you_section')}}',
                                                    `<p>{{translate('if_disabled_this_product_will_be_removed_from_the_is_for_you_section')}}</p>`,
                                                    `<p>{{translate('if_enabled_this_product_will_be_shown_in_the_is_for_you_section')}}</p>`)">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-soft-info fz-14">{{ ($product->for_you_priority == 11 || is_null($product->for_you_priority)) ? translate('not_set') : $product->for_you_priority }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
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
    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.js-select2-custom').select2();

            $('#priority_product_id').on('change', function() {
                let productId = $(this).val();
                if (productId) {
                    $.get({
                        url: "{{ route('admin.product.get-priority') }}",
                        data: { id: productId },
                        success: function (data) {
                            if (data.success) {
                                $('#for_you_priority_select').val(data.priority || 11);
                            }
                        }
                    });
                }
            });
        });

        function updatePriority() {
            let productId = $('#priority_product_id').val();
            let priority = $('#for_you_priority_select').val();

            if (!productId) {
                toastr.error('{{translate("please_select_a_product")}}');
                return;
            }

            if (!priority) {
                toastr.error('{{translate("please_choose_a_priority")}}');
                return;
            }

            $.ajax({
                url: "{{route('admin.product.for-you-priority')}}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: productId,
                    priority: priority
                },
                success: function (data) {
                    if (data.success) {
                        toastr.success('{{translate("priority_updated_successfully")}}');
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error('{{translate("priority_update_failed")}}');
                    }
                },
                error: function () {
                    toastr.error('{{translate("priority_update_failed")}}');
                }
            });
        }

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
                    if (data.success) {
                        toastr.success('{{translate("is_for_you_status_updated_successfully")}}');
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error('{{translate("is_for_you_status_update_failed")}}');
                    }
                }
            });
        });

        function toogleStatusModal(e, toggle_id, on_image, off_image, on_title, off_title, on_message, off_message) {
            e.preventDefault();
            if ($('#'+toggle_id).is(':checked')) {
                $('#toggle-title').empty().append(on_title);
                $('#toggle-message').empty().append(on_message);
                $('#toggle-image').attr('src', "{{asset('assets/back-end/img/modal')}}/"+on_image);
                $('#toggle-ok-button').attr('toggle-ok-button', toggle_id);
            } else {
                $('#toggle-title').empty().append(off_title);
                $('#toggle-message').empty().append(off_message);
                $('#toggle-image').attr('src', "{{asset('assets/back-end/img/modal')}}/"+off_image);
                $('#toggle-ok-button').attr('toggle-ok-button', toggle_id);
            }
            $('#toggle-modal').modal('show');
        }
    </script>
@endpush

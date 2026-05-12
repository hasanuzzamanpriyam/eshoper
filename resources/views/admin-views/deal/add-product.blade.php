@extends('layouts.back-end.app')
@section('title', translate('deal_Product'))
@push('css_or_js')
    <link href="{{ asset('assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/custom.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize">
            <img src="{{asset('assets/back-end/img/inhouse-product-list.png')}}" class="mb-1 mr-1" alt="">
            {{translate('add_new_product')}}
        </h2>
    </div>
    <!-- End Page Title -->

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 text-capitalize">{{$deal['title']}}</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.deal.add-product',[$deal['id']])}}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label for="name" class="title-color">{{ translate('products')}}</label>
                                    <div class="dropdown select-product-search w-100">
                                        <div class="selected-product-ids">
                                            <input type="hidden" class="product_id" name="product_id[]">
                                        </div>
                                        <button class="form-control text-start dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{translate('select_Product')}}

                                        </button>
                                        <div class="dropdown-menu w-100 px-2">
                                            <div class="search-form mb-3">
                                                <button type="button" class="btn"><i class="tio-search"></i></button>
                                                <input type="text" class="js-form-search form-control search-bar-input" placeholder="{{translate('search menu')}}...">
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="select-all-product">
                                                    <label class="form-check-label" for="select-all-product">{{translate('select_all')}}</label>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column gap-3 max-h-70vh overflow-y-auto overflow-x-hidden search-result-box" data-has-more="{{ $products->hasMorePages() ? 'true' : 'false' }}">
                                                @foreach ($products as $key => $product)
                                                    <div class="select-product-item media gap-3 border-bottom pb-2 cursor-pointer" data-id="{{$product['id']}}">
                                                        <div class="d-flex align-items-center">
                                                            <input type="checkbox" class="product-checkbox" value="{{$product['id']}}">
                                                        </div>
                                                        <img class="avatar avatar-xl border" width="75"
                                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                                                         alt="">
                                                        <div class="media-body d-flex flex-column gap-1">
                                                            <h6 class="product-id" hidden>{{$product['id']}}</h6>
                                                            <h6 class="fz-13 mb-1 text-truncate custom-width product-name">{{$product['name']}}</h6>
                                                            <div class="fz-10">{{translate('category')}} : {{isset($product->category) ? $product->category->name : translate('category_not_found') }}</div>
                                                            <div class="fz-10">{{translate('brand')}} : {{isset($product->brand) ? $product->brand->name : translate('brands_not_found') }}</div>
                                                            @if ($product->added_by == "seller")
                                                                <div class="fz-10">{{translate('shop')}} : {{isset($product->seller) ? $product->seller->shop->name : translate('shop_not_found') }}</div>
                                                            @else
                                                                <div class="fz-10">{{translate('shop')}} : {{$web_config['name']->value}}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label for="priority" class="title-color">{{ translate('priority')}} (1-10)</label>
                                    <select class="form-control" name="priority" id="priority">
                                        <option value="1">1 - {{ translate('highest_priority')}}</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10" selected>10 - {{ translate('lowest_priority')}}</option>
                                    </select>
                                    <small class="text-muted">{{ translate('priority_description')}}</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn--primary px-4">{{ translate('add')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <h5 class="mb-0 text-capitalize">
                        {{ translate('product_Table')}}
                        <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $deal_products->total() }}</span>
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100" cellspacing="0">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{ translate('name')}}</th>
                                <th>{{ translate('price')}}</th>
                                <th>{{ translate('priority')}}</th>
                                <th class="text-center">{{ translate('action')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($deal_products as $k=>$product)
                            <tr>
                                <td>{{$deal_products->firstitem()+$k}}</td>
                                <td><a href="#" target="_blank" class="font-weight-semibold title-color hover-c1">{{$product['name']}}</a></td>
                                <td>{{\App\CPU\BackEndHelper::usd_to_currency($product['unit_price'])}}</td>
                                <td>
                                    @if(isset($product->flash_deal_product) && $product->flash_deal_product->isNotEmpty())
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-{{ $product->flash_deal_product->first()->priority <= 3 ? 'danger' : ($product->flash_deal_product->first()->priority <= 6 ? 'warning' : 'secondary') }}">
                                                {{ $product->flash_deal_product->first()->priority }}
                                            </span>
                                            <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm edit-priority"
                                               data-product-id="{{$product['id']}}"
                                               data-deal-id="{{$deal['id']}}"
                                               data-current-priority="{{$product->flash_deal_product->first()->priority}}"
                                               title="{{ translate('edit_priority') }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="badge badge-secondary">10</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a  title="{{ translate ('delete')}}"
                                            class="btn btn-outline-danger btn-sm delete"
                                            id="{{$product['id']}}">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table>
                        <tfoot>
                            {!! $deal_products->links() !!}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{asset('assets/back-end')}}/js/select2.min.js"></script>
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.deal.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate("status_updated_successfully")}}');
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{translate('are_you_sure_remove_this_product')}}?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{translate('yes_delete_it')}}!",
                cancelButtonText: '{{ translate("cancel") }}',
                type: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.deal.delete-product')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function (data) {
                            toastr.success("{{translate('product_removed_successfully')}}");
                            location.reload();
                        }
                    });
                }
            })
        });

        // Edit priority functionality
        $(document).on('click', '.edit-priority', function (e) {
            console.log('Edit priority button clicked');
            e.preventDefault();
            let productId = $(this).data('product-id');
            let dealId = $(this).data('deal-id');
            let currentPriority = $(this).data('current-priority');
            console.log('Product ID:', productId, 'Deal ID:', dealId, 'Current Priority:', currentPriority);
            console.log('Swal is defined:', typeof Swal !== 'undefined');

            if (typeof Swal === 'undefined') {
                console.error('SweetAlert is not loaded');
                alert('SweetAlert is not loaded. Please check if the library is included.');
                return;
            }

            console.log('About to call Swal.fire');
            try {
                const result = Swal.fire({
                    title: "Edit Priority",
                    input: 'select',
                    inputOptions: {
                        '1': '1 - Highest priority',
                        '2': '2',
                        '3': '3',
                        '4': '4',
                        '5': '5',
                        '6': '6',
                        '7': '7',
                        '8': '8',
                        '9': '9',
                        '10': '10 - Lowest priority'
                    },
                    inputValue: currentPriority,
                    showCancelButton: true,
                    confirmButtonText: "Update",
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                });

                console.log('Swal.fire returned a promise:', result);
                result.then((result) => {
                    console.log('Swal.then called with result:', result);
                    console.log('result.value:', result.value);
                    console.log('result.isConfirmed:', result.isConfirmed);
                    console.log('result.isDismissed:', result.isDismissed);

                if (result.value !== undefined) {
                    console.log('About to make AJAX call with priority:', result.value);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.deal.update-priority')}}",
                        method: 'POST',
                        data: {
                            product_id: productId,
                            deal_id: dealId,
                            priority: result.value
                        },
                        success: function (response) {
                            console.log('Response:', response);
                            if (response.success === 1) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message || 'Failed to update priority');
                            }
                        },
                        error: function (xhr) {
                            console.log('Error:', xhr);
                            let errorMessage = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                errorMessage = '';
                                for (let key in errors) {
                                    errorMessage += errors[key][0] + '\n';
                                }
                            }
                            toastr.error(errorMessage);
                        }
                    });
                } else {
                    console.log('User cancelled the dialog');
                }
            });
            } catch (error) {
                console.error('Error in Swal.fire:', error);
                alert('Error opening dialog: ' + error.message);
            }
        });

        $(document).ready(function() {
            let selectProductSearch = $('.select-product-search');
            let selectedIds = [];

            selectProductSearch.on('click', '.select-product-item', function (e) {
                let productId = $(this).data('id').toString();
                if ($(e.target).is('.product-checkbox')) {
                    toggleId(productId, $(e.target).prop('checked'));
                    return;
                }
                let checkbox = $(this).find('.product-checkbox');
                let newState = !checkbox.prop('checked');
                checkbox.prop('checked', newState);
                toggleId(productId, newState);
            });

            $('#select-all-product').on('change', function () {
                let isChecked = $(this).prop('checked');
                $('.product-checkbox').each(function () {
                    $(this).prop('checked', isChecked);
                    toggleId($(this).val(), isChecked);
                });
            });

            function toggleId(id, isSelected) {
                id = id.toString();
                if (isSelected) {
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                } else {
                    selectedIds = selectedIds.filter(item => item !== id);
                }
                updateHiddenInputs();
            }

            function updateHiddenInputs() {
                let container = $('.selected-product-ids');
                container.empty();

                if (selectedIds.length > 0) {
                    selectedIds.forEach(id => {
                        container.append(`<input type="hidden" name="product_id[]" value="${id}">`);
                    });
                    selectProductSearch.find('button.dropdown-toggle').text(selectedIds.length + " {{translate('products_selected')}}");
                } else {
                    selectProductSearch.find('button.dropdown-toggle').text("{{translate('select_Product')}}");
                }

                // Update Select All checkbox state
                let allVisibleChecked = $('.product-checkbox').length > 0 && $('.product-checkbox:not(:checked)').length === 0;
                $('#select-all-product').prop('checked', allVisibleChecked);
            }

            // Prevent dropdown from closing when clicking inside
            selectProductSearch.on('click', '.dropdown-menu', function (e) {
                e.stopPropagation();
            });

            let page = 1;
            let hasMore = $('.search-result-box').data('has-more') == true;
            let isLoading = false;
            let searchKey = '';

            $('.search-result-box').on('scroll', function () {
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 50) {
                    if (hasMore && !isLoading) {
                        page++;
                        fetchProducts(true);
                    }
                }
            });

            let searchTimer;
            $('.search-bar-input').on('keyup', function () {
                let key = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    searchKey = key;
                    page = 1;
                    fetchProducts(false);
                }, 300);
            });

            function fetchProducts(append = false) {
                isLoading = true;
                if (!append) {
                    $('.search-result-box').html('<div class="text-center p-3"><i class="tio-dev-loader-1-spinner tio-spin"></i> Loading...</div>');
                }
                
                $.get("{{route('admin.deal.search-product')}}", {
                    name: searchKey,
                    page: page
                }, (response) => {
                    if (append) {
                        $('.search-result-box').append(response.result);
                    } else {
                        $('.search-result-box').html(response.result);
                    }
                    hasMore = response.hasMore;
                    syncCheckboxes();
                    isLoading = false;
                });
            }

            function syncCheckboxes() {
                $('.product-checkbox').each(function () {
                    if (selectedIds.includes($(this).val().toString())) {
                        $(this).prop('checked', true);
                    }
                });

                let allVisibleChecked = $('.product-checkbox').length > 0 && $('.product-checkbox:not(:checked)').length === 0;
                $('#select-all-product').prop('checked', allVisibleChecked);
            }
        });
    </script>
@endpush

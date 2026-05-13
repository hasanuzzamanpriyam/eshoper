@extends('layouts.back-end.app')
@section('title', translate('deal_Product'))
@push('css_or_js')
    <link href="{{ asset('assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/custom.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Side Drawer Styles */
        .side-drawer {
            position: fixed;
            top: 0;
            right: -500px;
            width: 500px;
            max-width: 100%;
            height: 100vh;
            background: #fff;
            box-shadow: -10px 0 30px rgba(0,0,0,0.2);
            z-index: 999999 !important; /* Extremely high to stay on top */
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            border-left: 1px solid #ddd;
        }
        .side-drawer.open {
            right: 0;
        }
        .side-drawer-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .side-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .side-drawer-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 999998 !important;
            display: none;
        }
        .drawer-overlay.show {
            display: block;
        }

        /* Premium Product Item */
        .premium-product-item {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #eee;
            transition: all 0.2s ease;
            margin-bottom: 10px;
            cursor: pointer;
            background: #fff;
        }
        .premium-product-item:hover {
            border-color: #377dff;
            background: #f0f5ff;
        }
        .premium-product-item.selected {
            border-color: #377dff;
            background: #eef4ff;
        }
        .premium-product-item img {
            border-radius: 6px;
            object-fit: cover;
        }
        .premium-product-item .info h6 {
            font-weight: 600;
            margin-bottom: 2px;
            color: #333;
        }
        .premium-product-item .info span {
            font-size: 11px;
            color: #777;
        }
        .custom-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            background: #f1f1f1;
            color: #555;
        }
    </style>
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
                                <div class="col-md-3 mt-3">
                                    <label for="shop_id" class="title-color">{{ translate('select_Store')}}</label>
                                    <select class="form-control js-select-shop" name="shop_id" id="shop_id">
                                        <option value="" disabled selected>{{translate('select_Store')}}</option>
                                        @if($inhouse_product_count > 0)
                                            <option value="inhouse">{{translate('inhouse')}}</option>
                                        @endif
                                        @foreach($shops as $shop)
                                            <option value="{{$shop->id}}">{{$shop->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="brand_id" class="title-color">{{ translate('select_Brand')}}</label>
                                    <select class="form-control js-select-brand" name="brand_id" id="brand_id">
                                        <option value="" disabled selected>{{translate('select_Brand')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="category_id" class="title-color">{{ translate('select_Category')}}</label>
                                    <select class="form-control js-select-category" name="category_id" id="category_id">
                                        <option value="" disabled selected>{{translate('select_Category')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="discounted" class="title-color">{{ translate('discounted_Products')}}</label>
                                    <button type="button" class="form-control btn btn-outline-primary js-select-discounted" id="discounted_btn">
                                        {{translate('show_discounted_products')}}
                                    </button>
                                </div>
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

                                            <div class="d-flex flex-column gap-3 max-h-40vh overflow-y-auto overflow-x-hidden search-result-box" data-has-more="{{ $products->hasMorePages() ? 'true' : 'false' }}">
                                                @include('admin-views.partials._search-product', ['products' => $products])
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
                <div class="px-3 py-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="mb-0 text-capitalize">
                        {{ translate('product_Table')}}
                        <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $deal_products->total() }}</span>
                    </h5>
                    <button type="button" class="btn btn-outline-danger btn-sm js-delete-all-products"
                            data-url="{{ route('admin.deal.delete-all-products', [$deal['id']]) }}">
                        <i class="tio-delete-outlined"></i> {{ translate('delete_all_products') }}
                    </button>
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
                        <tbody id="deal-product-table-body">
                            @include('admin-views.deal.partials._deal-product-table', ['deal_products' => $deal_products, 'deal' => $deal])
                        </tbody>
                    </table>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {{ $deal_products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

<!-- Side Drawer - Moved outside content for better positioning -->
<div class="drawer-overlay" id="drawerOverlay"></div>
<div class="side-drawer" id="storeProductDrawer">
    <div class="side-drawer-header">
        <div>
            <h5 class="mb-1">{{translate('select_Products')}}</h5>
            <p class="text-muted small mb-0">{{translate('search_and_select_to_add')}}</p>
        </div>
        <button type="button" class="close js-close-drawer">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="side-drawer-body">
        <div class="search-form mb-4 sticky-top bg-white pt-2">
            <div class="input-group input-group-merge border">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="tio-search"></i>
                    </div>
                </div>
                <input type="text" class="form-control modal-search-bar-input" placeholder="{{translate('search_products')}}...">
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="modal-select-all-product">
                <label class="form-check-label font-weight-bold" for="modal-select-all-product">{{translate('select_all')}}</label>
            </div>
            <div class="selected-count text-primary font-weight-bold">0 {{translate('selected')}}</div>
        </div>

        <div class="modal-search-result-box">
            <div class="text-center p-5">
                <img src="{{asset('assets/back-end/img/info.png')}}" width="40" class="mb-2" alt="">
                <p class="text-muted">{{translate('please_select_a_store_first')}}</p>
            </div>
        </div>
    </div>
    <div class="side-drawer-footer">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light flex-grow-1 js-close-drawer">{{translate('cancel')}}</button>
            <button type="button" class="btn btn--primary flex-grow-1 js-add-selected-products">{{translate('add_to_deal')}}</button>
        </div>
    </div>
</div>

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
                if (isChecked) {
                    // Fetch all IDs for search
                    $(this).parent().find('label').text("{{translate('selecting_all')}}...");
                    $.get("{{route('admin.deal.get-all-product-ids')}}", {
                        name: searchKey,
                        deal_id: "{{$deal['id']}}"
                    }, (response) => {
                        selectedIds = response.ids.map(String);
                        updateHiddenInputs();
                        syncCheckboxes();
                        $(this).parent().find('label').text("{{translate('select_all')}}");
                    });
                } else {
                    $('.product-checkbox').each(function () {
                        $(this).prop('checked', false);
                        toggleId($(this).val(), false);
                    });
                    $(this).parent().find('label').text("{{translate('select_all')}}");
                }
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
                    deal_id: "{{$deal['id']}}",
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

            // Store selection and Modal logic
            let modalSelectedIds = [];
            let modalPage = 1;
            let modalHasMore = false;
            let modalIsLoading = false;
            let modalSearchKey = '';
            let currentShopId = '';
            let currentBrandId = '';
            let currentCategoryId = '';
            let currentDiscounted = false;

            // Fetch brands on page load
            $.get("{{route('admin.deal.get-brands')}}", {
                deal_id: "{{$deal['id']}}"
            }, (response) => {
                let brandSelect = $('#brand_id');
                brandSelect.empty();
                brandSelect.append('<option value="" disabled selected>{{translate('select_Brand')}}</option>');
                response.brands.forEach(brand => {
                    brandSelect.append(`<option value="${brand.id}">${brand.name}</option>`);
                });
            });

            // Fetch categories on page load
            $.get("{{route('admin.deal.get-categories')}}", {
                deal_id: "{{$deal['id']}}"
            }, (response) => {
                let categorySelect = $('#category_id');
                categorySelect.empty();
                categorySelect.append('<option value="" disabled selected>{{translate('select_Category')}}</option>');
                response.categories.forEach(category => {
                    categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                });
            });

            $('.js-select-shop').on('change', function () {
                currentShopId = $(this).val();
                currentBrandId = ''; // Reset brand when shop is selected
                currentCategoryId = ''; // Reset category when shop is selected
                currentDiscounted = false; // Reset discounted when shop is selected
                $('#brand_id').val('');
                $('#category_id').val('');
                $('#discounted_btn').removeClass('btn-primary').addClass('btn-outline-primary');
                if (currentShopId) {
                    $('#storeProductDrawer').addClass('open');
                    $('#drawerOverlay').addClass('show');
                    modalPage = 1;
                    modalSelectedIds = [];
                    updateModalSelectedCount();
                    fetchModalProducts(false);
                }
            });

            $('.js-select-brand').on('change', function () {
                currentBrandId = $(this).val();
                currentShopId = ''; // Reset shop when brand is selected
                currentCategoryId = ''; // Reset category when brand is selected
                currentDiscounted = false; // Reset discounted when brand is selected
                $('#shop_id').val('');
                $('#category_id').val('');
                $('#discounted_btn').removeClass('btn-primary').addClass('btn-outline-primary');
                if (currentBrandId) {
                    $('#storeProductDrawer').addClass('open');
                    $('#drawerOverlay').addClass('show');
                    modalPage = 1;
                    modalSelectedIds = [];
                    updateModalSelectedCount();
                    fetchModalProducts(false);
                }
            });

            $('.js-select-category').on('change', function () {
                currentCategoryId = $(this).val();
                currentShopId = ''; // Reset shop when category is selected
                currentBrandId = ''; // Reset brand when category is selected
                currentDiscounted = false; // Reset discounted when category is selected
                $('#shop_id').val('');
                $('#brand_id').val('');
                $('#discounted_btn').removeClass('btn-primary').addClass('btn-outline-primary');
                if (currentCategoryId) {
                    $('#storeProductDrawer').addClass('open');
                    $('#drawerOverlay').addClass('show');
                    modalPage = 1;
                    modalSelectedIds = [];
                    updateModalSelectedCount();
                    fetchModalProducts(false);
                }
            });

            $('.js-select-discounted').on('click', function () {
                currentDiscounted = !currentDiscounted;
                currentShopId = ''; // Reset shop when discounted is selected
                currentBrandId = ''; // Reset brand when discounted is selected
                currentCategoryId = ''; // Reset category when discounted is selected
                $('#shop_id').val('');
                $('#brand_id').val('');
                $('#category_id').val('');

                if (currentDiscounted) {
                    $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                    $('#storeProductDrawer').addClass('open');
                    $('#drawerOverlay').addClass('show');
                    modalPage = 1;
                    modalSelectedIds = [];
                    updateModalSelectedCount();
                    fetchModalProducts(false);
                } else {
                    $(this).removeClass('btn-primary').addClass('btn-outline-primary');
                    $('#storeProductDrawer').removeClass('open');
                    $('#drawerOverlay').removeClass('show');
                }
            });

            $('.js-close-drawer, #drawerOverlay').on('click', function () {
                $('#storeProductDrawer').removeClass('open');
                $('#drawerOverlay').removeClass('show');
            });

            $('.modal-search-bar-input').on('keyup', function () {
                let key = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    modalSearchKey = key;
                    modalPage = 1;
                    fetchModalProducts(false);
                }, 300);
            });

            $('.side-drawer-body').on('scroll', function () {
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 50) {
                    if (modalHasMore && !modalIsLoading) {
                        modalPage++;
                        fetchModalProducts(true);
                    }
                }
            });

            function fetchModalProducts(append = false) {
                modalIsLoading = true;
                if (!append) {
                    $('.modal-search-result-box').html('<div class="text-center p-3"><i class="tio-dev-loader-1-spinner tio-spin"></i> Loading...</div>');
                }

                let requestData = {
                    name: modalSearchKey,
                    deal_id: "{{$deal['id']}}",
                    page: modalPage
                };

                if (currentShopId) {
                    requestData.shop_id = currentShopId;
                }

                if (currentBrandId) {
                    requestData.brand_id = currentBrandId;
                }

                if (currentCategoryId) {
                    requestData.category_id = currentCategoryId;
                }

                if (currentDiscounted) {
                    requestData.discounted = 1;
                }

                $.get("{{route('admin.deal.search-product')}}", requestData, (response) => {
                    if (append) {
                        $('.modal-search-result-box').append(response.result);
                    } else {
                        $('.modal-search-result-box').html(response.result);
                    }
                    modalHasMore = response.hasMore;
                    syncModalCheckboxes();
                    modalIsLoading = false;
                });
            }

            $('.modal-search-result-box').on('click', '.select-product-item', function (e) {
                let productId = $(this).data('id').toString();
                if ($(e.target).is('.product-checkbox')) {
                    toggleModalId(productId, $(e.target).prop('checked'));
                    return;
                }
                let checkbox = $(this).find('.product-checkbox');
                let newState = !checkbox.prop('checked');
                checkbox.prop('checked', newState);
                toggleModalId(productId, newState);
            });

            $('#modal-select-all-product').on('change', function () {
                let isChecked = $(this).prop('checked');
                if (isChecked) {
                    // Fetch all IDs for current shop/brand/category/discounted and search
                    $(this).parent().find('label').text("{{translate('selecting_all')}}...");
                    let requestData = {
                        name: modalSearchKey,
                        deal_id: "{{$deal['id']}}"
                    };

                    if (currentShopId) {
                        requestData.shop_id = currentShopId;
                    }

                    if (currentBrandId) {
                        requestData.brand_id = currentBrandId;
                    }

                    if (currentCategoryId) {
                        requestData.category_id = currentCategoryId;
                    }

                    if (currentDiscounted) {
                        requestData.discounted = 1;
                    }

                    $.get("{{route('admin.deal.get-all-product-ids')}}", requestData, (response) => {
                        modalSelectedIds = response.ids.map(String);
                        syncModalCheckboxes();
                        $(this).parent().find('label').text("{{translate('select_all')}}");
                    });
                } else {
                    modalSelectedIds = [];
                    $('.modal-search-result-box .product-checkbox').prop('checked', false);
                    updateModalSelectedCount();
                    $(this).parent().find('label').text("{{translate('select_all')}}");
                }
            });

            function toggleModalId(id, isSelected) {
                id = id.toString();
                if (isSelected) {
                    if (!modalSelectedIds.includes(id)) {
                        modalSelectedIds.push(id);
                    }
                } else {
                    modalSelectedIds = modalSelectedIds.filter(item => item !== id);
                }
                updateModalSelectedCount();
            }

            function updateModalSelectedCount() {
                $('.selected-count').text(modalSelectedIds.length + " {{translate('products_selected')}}");
                let allVisibleChecked = $('.modal-search-result-box .product-checkbox').length > 0 && $('.modal-search-result-box .product-checkbox:not(:checked)').length === 0;
                $('#modal-select-all-product').prop('checked', allVisibleChecked);
            }

            function syncModalCheckboxes() {
                $('.modal-search-result-box .product-checkbox').each(function () {
                    if (modalSelectedIds.includes($(this).val().toString())) {
                        $(this).prop('checked', true);
                    }
                });
                updateModalSelectedCount();
            }

            $('.js-add-selected-products').on('click', function () {
                if (modalSelectedIds.length > 0) {
                    modalSelectedIds.forEach(id => {
                        if (!selectedIds.includes(id)) {
                            selectedIds.push(id);
                        }
                    });
                    updateHiddenInputs();
                    syncCheckboxes();
                    $('#storeProductDrawer').removeClass('open');
                    $('#drawerOverlay').removeClass('show');
                    toastr.success(modalSelectedIds.length + " {{translate('products_added_to_selection')}}");
                } else {
                    toastr.warning("{{translate('please_select_at_least_one_product')}}");
                }
            });


            $('.js-delete-all-products').on('click', function () {
                let url = $(this).data('url');
                Swal.fire({
                    title: '{{translate("are_you_sure")}}?',
                    text: '{{translate("you_want_to_delete_all_products_from_this_flash_deal")}}?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: '{{ translate("no") }}',
                    confirmButtonText: '{{ translate("yes") }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.get(url, function (response) {
                            if (response.success) {
                                toastr.success('{{translate("all_products_deleted_successfully")}}');
                                location.reload();
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush

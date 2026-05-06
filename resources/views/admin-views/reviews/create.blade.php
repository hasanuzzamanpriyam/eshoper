@extends('layouts.back-end.app')

@section('title', translate('add_Review'))

@push('css_or_js')
    <link href="{{asset('assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/select2/css/select2.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <i class="tio-star"></i>
                {{translate('add_Review')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.reviews.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="product_sku" class="title-color">{{translate('product_SKU')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="product_sku" class="form-control" id="product_sku" placeholder="{{translate('ex')}} : SKU-001" value="{{old('product_sku')}}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="customer_id" class="title-color">{{translate('customer')}} ({{translate('optional')}})</label>
                                    <select class="js-data-example-ajax form-control" name="customer_id" id="customer_id">
                                        <option value="">--- {{translate('select_Customer')}} ---</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="rating" class="title-color">{{translate('rating')}} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="rating" id="rating" required>
                                        <option value="5" {{old('rating') == 5 ? 'selected' : ''}}>5 {{translate('stars')}}</option>
                                        <option value="4" {{old('rating') == 4 ? 'selected' : ''}}>4 {{translate('stars')}}</option>
                                        <option value="3" {{old('rating') == 3 ? 'selected' : ''}}>3 {{translate('stars')}}</option>
                                        <option value="2" {{old('rating') == 2 ? 'selected' : ''}}>2 {{translate('stars')}}</option>
                                        <option value="1" {{old('rating') == 1 ? 'selected' : ''}}>1 {{translate('star')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="status" class="title-color">{{translate('status')}}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="1" {{old('status') == 1 ? 'selected' : ''}}>{{translate('active')}}</option>
                                        <option value="0" {{old('status') == 0 ? 'selected' : ''}}>{{translate('inactive')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="comment" class="title-color">{{translate('comment')}}</label>
                                    <textarea name="comment" class="form-control" id="comment" rows="4" placeholder="{{translate('ex')}} : {{translate('great_product')}}">{{old('comment')}}</textarea>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary px-5">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('assets/back-end/js/tags-input.min.js')}}"></script>
    <script src="{{ asset('assets/select2/js/select2.min.js')}}"></script>
    <script>
        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.reviews.customer-list-search')}}',
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });
    </script>
@endpush

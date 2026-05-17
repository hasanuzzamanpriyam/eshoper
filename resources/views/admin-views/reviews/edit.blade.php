@extends('layouts.back-end.app')

@section('title', translate('edit_Review'))

@push('css_or_js')
    <link href="{{asset('assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/select2/css/select2.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <i class="tio-edit"></i>
                {{translate('edit_Review')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.reviews.update', [$review->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="product_sku" class="title-color">{{translate('product_SKU')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="product_sku" class="form-control" id="product_sku" placeholder="{{translate('ex')}} : SKU-001" value="{{old('product_sku', $review->product ? $review->product->code : '')}}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="customer_id" class="title-color">{{translate('customer')}} ({{translate('optional')}})</label>
                                    <select class="js-data-example-ajax form-control" name="customer_id" id="customer_id">
                                        @if ($review->customer)
                                            <option value="{{ $review->customer_id }}" selected>{{ $review->customer->f_name . ' ' . $review->customer->l_name }}</option>
                                        @else
                                            <option value="">--- {{translate('select_Customer')}} ---</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="reviewer_name" class="title-color">{{translate('reviewer_name')}} ({{translate('optional')}})</label>
                                    <input type="text" name="reviewer_name" class="form-control" id="reviewer_name" placeholder="{{translate('ex')}} : John Doe" value="{{old('reviewer_name', $review->reviewer_name)}}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="rating" class="title-color">{{translate('rating')}} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="rating" id="rating" required>
                                        <option value="5" {{old('rating', $review->rating) == 5 ? 'selected' : ''}}>5 {{translate('stars')}}</option>
                                        <option value="4" {{old('rating', $review->rating) == 4 ? 'selected' : ''}}>4 {{translate('stars')}}</option>
                                        <option value="3" {{old('rating', $review->rating) == 3 ? 'selected' : ''}}>3 {{translate('stars')}}</option>
                                        <option value="2" {{old('rating', $review->rating) == 2 ? 'selected' : ''}}>2 {{translate('stars')}}</option>
                                        <option value="1" {{old('rating', $review->rating) == 1 ? 'selected' : ''}}>1 {{translate('star')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="status" class="title-color">{{translate('status')}}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="1" {{old('status', $review->status) == 1 ? 'selected' : ''}}>{{translate('active')}}</option>
                                        <option value="0" {{old('status', $review->status) == 0 ? 'selected' : ''}}>{{translate('inactive')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="created_at" class="title-color">{{translate('review_date')}} ({{translate('optional')}})</label>
                                    <input type="datetime-local" name="created_at" class="form-control" id="created_at" value="{{old('created_at', $review->created_at ? $review->created_at->format('Y-m-d\TH:i') : '')}}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="reviewer_image" class="title-color">{{translate('reviewer_image')}} ({{translate('optional')}})</label>
                                    <input type="file" name="reviewer_image" class="form-control" id="reviewer_image" accept="image/*">
                                    
                                    @if($review->reviewer_image)
                                        <input type="hidden" name="is_existing_reviewer_image_removed" id="is_existing_reviewer_image_removed" value="0">
                                        <div class="mt-2 existing-reviewer-image-container d-flex align-items-center gap-2">
                                            <input type="hidden" name="existing_reviewer_image" value="{{ $review->reviewer_image }}">
                                            <img width="60" height="60" class="rounded-circle object-cover" src="{{ asset('storage/profile') }}/{{ $review->reviewer_image }}" alt="Reviewer Image">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.existing-reviewer-image-container').html(''); $('#is_existing_reviewer_image_removed').val(1)">
                                                <i class="tio-delete"></i> {{ translate('remove') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="comment" class="title-color">{{translate('comment')}}</label>
                                    <textarea name="comment" class="form-control" id="comment" rows="4" placeholder="{{translate('ex')}} : {{translate('great_product')}}">{{old('comment', $review->comment)}}</textarea>
                                </div>
                                
                                <!-- Existing Images -->
                                @if ($review->attachment)
                                    @php($attachments = json_decode($review->attachment, true))
                                    @if (!empty($attachments))
                                        <div class="col-md-12 mb-4">
                                            <label class="title-color text-capitalize font-weight-bold mb-2">{{ translate('existing_images') }}</label>
                                            <div class="row g-2">
                                                @foreach ($attachments as $key => $img)
                                                    <div class="col-sm-12 col-md-3 existing-image-container">
                                                        <div class="custom_upload_input position-relative border-dashed-2" style="height: 120px;">
                                                            <input type="hidden" name="existing_attachment[]" value="{{ $img }}">
                                                            <span class="btn btn-outline-danger btn-sm square-btn position-absolute" style="top: 5px; right: 5px; z-index: 99;" onclick="$(this).closest('.existing-image-container').remove()">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2 border-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                                <img class="h-100 aspect-1 bg-white object-cover" src="{{ asset('storage/review') }}/{{ $img }}" onerror="this.src='{{ asset('assets/back-end/img/image-place-holder.png') }}'">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Upload New Images -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_new_image') }}</label>
                                        <div class="row g-2" id="additional_Image_Section">
                                            <div class="col-sm-12 col-md-4">
                                                <div class="custom_upload_input position-relative border-dashed-2">
                                                    <input type="file" name="attachment[]" class="custom-upload-input-file"
                                                        data-index="1" data-imgpreview="additional_Image_1"
                                                        accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                        onchange="addMoreImage(this, '#additional_Image_Section')">
                                                    
                                                    <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn" style="display: none">
                                                        <i class="tio-delete"></i>
                                                    </span>
                                                    
                                                    <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                        <img id="additional_Image_1" class="h-auto aspect-1 bg-white"
                                                            src="{{asset('assets/back-end/img/400x400/img2.jpg')}}"
                                                            onerror="this.classList.add('d-none')">
                                                    </div>
                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                            <img src="{{asset('assets/back-end/img/icons/product-upload-icon.svg')}}" class="w-50">
                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('admin.reviews.list') }}" class="btn btn-secondary px-5">{{translate('cancel')}}</a>
                                <button type="submit" class="btn btn--primary px-5">{{translate('update')}}</button>
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

        function addMoreImage(thisData, targetSection) {
            let $fileInputs = $(targetSection + " input[type='file']");
            let nonEmptyCount = 0;

            $fileInputs.each(function () {
                if (parseFloat($(this).prop('files').length) == 0) {
                    nonEmptyCount++;
                }
            });

            document.getElementById(thisData.dataset.imgpreview).setAttribute("src", window.URL.createObjectURL(thisData.files[0]));
            document.getElementById(thisData.dataset.imgpreview).classList.remove('d-none');

            if (nonEmptyCount == 0) {
                let dataset_index = parseInt(thisData.dataset.index) + 1;

                let newHtmlData = `<div class="col-sm-12 col-md-4">
                                <div class="custom_upload_input position-relative border-dashed-2">
                                    <input type="file" name="${thisData.name}" class="custom-upload-input-file" data-index="${dataset_index}" data-imgpreview="additional_Image_${dataset_index}"
                                        accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" onchange="addMoreImage(this, '${targetSection}')">

                                    <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn" style="display: none">
                                        <i class="tio-delete"></i>
                                    </span>

                                    <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                        <img id="additional_Image_${dataset_index}" class="h-auto aspect-1 bg-white" src="{{ asset('assets/back-end/img/400x400/img2.jpg') }}" onerror="this.classList.add('d-none')">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <img src="{{asset('assets/back-end/img/icons/product-upload-icon.svg')}}" class="w-50">
                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                $(targetSection).append(newHtmlData);
            }

            $('.custom-upload-input-file').on('change', function () {
                if (parseFloat($(this).prop('files').length) != 0) {
                    let $parentDiv = $(this).closest('div');
                    $parentDiv.find('.delete_file_input').fadeIn();
                }
            })

            $('.delete_file_input_section').click(function () {
                let $parentDiv = $(this).closest('div').parent().remove();
            });
        }

        $('.delete_file_input').click(function () {
            let $parentDiv = $(this).closest('div');
            $parentDiv.find('input[type="file"]').val('');
            $parentDiv.find('.img_area_with_preview img').attr("src", " ");
            $(this).hide();
        });

        $('.custom-upload-input-file').on('change', function () {
            if (parseFloat($(this).prop('files').length) != 0) {
                let $parentDiv = $(this).closest('div');
                $parentDiv.find('.delete_file_input').fadeIn();
            }
        })
    </script>
@endpush

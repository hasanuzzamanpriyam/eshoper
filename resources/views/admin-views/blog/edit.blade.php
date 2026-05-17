@extends('layouts.back-end.app')

@section('title', 'Edit Blog')

@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/css/select2.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/back-end/img/brand.png') }}" alt="">
                Blog Update
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog.update', [$blog['id']]) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="blog_category_id" class="title-color">Blog Category<span class="text-danger">*</span></label>
                                        <select name="blog_category_id" id="blog_category_id" class="form-control" required>
                                            <option value="" disabled>Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $blog['blog_category_id'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="heading" class="title-color">Heading<span class="text-danger">*</span></label>
                                        <input type="text" name="heading" class="form-control" id="heading" value="{{ $blog['heading'] }}" placeholder="Enter Heading" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author_name" class="title-color">Author Name</label>
                                        <input type="text" name="author_name" class="form-control" id="author_name" value="{{ $blog['author_name'] }}" placeholder="Enter Author Name">
                                    </div>
                                    {{-- Add Slug Field --}}
                                    <div class="form-group">
                                        <label for="slug" class="title-color">Slug<span class="text-danger">*</span></label>
                                        <input type="text" name="slug" id="slug" class="form-control" value="{{ $blog['slug'] ?? '' }}" placeholder="Enter Slug" required>
                                        <div id="slug-message" class="mt-1" style="font-size: 0.85rem;"></div>
                                    </div>
                                    {{-- End Slug Field --}}
                                    <div class="form-group">
                                        <label for="name" class="title-color">Blog Image</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileUpload">Choose File</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="ad_image" class="title-color">Advertisement Image</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="ad_image" id="customFileUploadAd" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileUploadAd">Choose File</label>
                                        </div>
                                        
                                        @if($blog->ad_image)
                                            <input type="hidden" name="is_existing_ad_image_removed" id="is_existing_ad_image_removed" value="0">
                                            <div class="mt-2 existing-ad-image-container d-flex align-items-center gap-2">
                                                <img width="80" height="80" class="rounded object-cover" src="{{ asset('storage/blog') }}/{{ $blog->ad_image }}" alt="Ad Image">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.existing-ad-image-container').html(''); $('#is_existing_ad_image_removed').val(1); $('#viewer_ad').attr('src', '{{ asset('assets/back-end/img/400x400/img2.jpg') }}')">
                                                    <i class="tio-delete"></i> {{ translate('remove') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="ad_link" class="title-color">Advertisement/Blog Link</label>
                                        <input type="text" name="ad_link" class="form-control" id="ad_link" value="{{ old('ad_link', $blog->ad_link) }}" placeholder="Enter link for advertisement/blog image (e.g. https://...)">
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_title" class="title-color">Meta Title <span class="text-muted">(Max 60 chars)</span></label>
                                        <input type="text" name="meta_title" class="form-control" id="meta_title" maxlength="60" value="{{ old('meta_title', $blog->meta_title) }}" placeholder="Enter Meta Title">
                                        <div class="text-right mt-1" style="font-size: 0.8rem;">
                                            <span id="meta_title_counter" class="text-primary">{{ strlen($blog->meta_title ?? '') }}</span>/60 characters
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_description" class="title-color">Meta Description <span class="text-muted">(Max 160 chars)</span></label>
                                        <textarea name="meta_description" class="form-control" id="meta_description" rows="3" maxlength="160" placeholder="Enter Meta Description">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                        <div class="text-right mt-1" style="font-size: 0.8rem;">
                                            <span id="meta_description_counter" class="text-primary">{{ strlen($blog->meta_description ?? '') }}</span>/160 characters
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex flex-wrap justify-content-around align-items-center gap-3">
                                        <div class="text-center">
                                            <label class="title-color d-block mb-2 font-weight-bold">Blog Image Preview</label>
                                            <img class="upload-img-view" id="viewer" style="max-height: 180px; width: auto;"
                                                src="{{ asset('storage/blog') }}/{{ $blog['image'] }}"
                                                onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'"
                                                alt="blog image" />
                                        </div>
                                        <div class="text-center">
                                            <label class="title-color d-block mb-2 font-weight-bold">Ad Image Preview</label>
                                            <img class="upload-img-view" id="viewer_ad" style="max-height: 180px; width: auto;"
                                                src="{{ $blog->ad_image ? asset('storage/blog/'.$blog->ad_image) : asset('assets/back-end/img/400x400/img2.jpg') }}"
                                                onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'"
                                                alt="ad image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="title-color">Description<span class="text-danger">*</span></label>
                                        <textarea name="description" class="textarea editor-textarea" id="editor" required>{{ $blog['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset" class="btn btn-secondary px-4">Reset</button>
                                <button type="submit" class="btn btn--primary px-4">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/back-end') }}/js/select2.min.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        if (window.CKEDITOR) {
            CKEDITOR.env.isCompatible = true;
            $('#editor').ckeditor({
                // CKEditor configuration
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLAd(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer_ad').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });

        $("#customFileUploadAd").change(function() {
            readURLAd(this);
            // If they upload a new image, mark existing removed as 0 and clean up existing preview container
            $('#is_existing_ad_image_removed').val(0);
            $('.existing-ad-image-container').html('');
        });

        // Meta title and description counters
        $('#meta_title').on('input', function () {
            let length = $(this).val().length;
            $('#meta_title_counter').text(length);
            if (length >= 60) {
                $('#meta_title_counter').removeClass('text-primary').addClass('text-danger');
            } else {
                $('#meta_title_counter').removeClass('text-danger').addClass('text-primary');
            }
        });

        $('#meta_description').on('input', function () {
            let length = $(this).val().length;
            $('#meta_description_counter').text(length);
            if (length >= 160) {
                $('#meta_description_counter').removeClass('text-primary').addClass('text-danger');
            } else {
                $('#meta_description_counter').removeClass('text-danger').addClass('text-primary');
            }
        });

        // Slug generation logic
        $('#heading').on('keyup', function () {
            let title = $(this).val();
            $('#slug').val(convertToSlug(title));
        });

        function convertToSlug(Text) {
            // Remove unwanted characters, convert to lower case, replace spaces with hyphens
            return Text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-');
        }

        $('#slug').on('keyup', function () {
            checkSlug($(this).val());
        });

        function checkSlug(slug) {
            if (slug.length > 0) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{ route('admin.blog.check-slug') }}',
                    data: {
                        slug: slug,
                        id: '{{ $blog['id'] }}'
                    },
                    success: function (data) {
                        if (data.exists) {
                            $('#slug-message').html('<span class="text-danger">This slug is already in use.</span>');
                        } else {
                            $('#slug-message').html('<span class="text-success">Slug is available.</span>');
                        }
                    }
                });
            } else {
                $('#slug-message').html('');
            }
        }

        // Ensure the slug field is populated on page load if heading exists and slug is empty
        $(document).ready(function() {
            if ($('#heading').val() && !$('#slug').val()) {
                $('#slug').val(convertToSlug($('#heading').val()));
            }
        });
    </script>
@endpush
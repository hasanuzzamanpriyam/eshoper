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
                                        <label for="heading" class="title-color">Heading<span class="text-danger">*</span></label>
                                        <input type="text" name="heading" class="form-control" id="heading" value="{{ $blog['heading'] }}" placeholder="Enter Heading" required>
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
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-center">
                                        <img class="upload-img-view" id="viewer"
                                            src="{{ asset('storage/blog') }}/{{ $blog['image'] }}"
                                            onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'"
                                            alt="blog image" />
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

        $("#customFileUpload").change(function() {
            readURL(this);
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
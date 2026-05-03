@extends('layouts.back-end.app')

@section('title', 'Edit Blog Category')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/back-end/img/brand.png') }}" alt="">
                Edit Blog Category
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-md-8 col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Update Category</h5>
                    </div>
                    <div class="card-body" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog-category.update', $category->id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="title-color">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="cat-name" class="form-control"
                                    value="{{ $category->name }}" placeholder="Enter Category Name" required>
                            </div>
                            <div class="form-group">
                                <label for="slug" class="title-color">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="cat-slug" class="form-control"
                                    value="{{ $category->slug }}" placeholder="Enter Slug" required>
                                <div id="slug-message" class="mt-1" style="font-size: 0.85rem;"></div>
                            </div>

                            <div class="d-flex gap-3">
                                <a href="{{ route('admin.blog-category.add-new') }}" class="btn btn-secondary px-4">Cancel</a>
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
    <script>
        // Auto-generate slug from name only when slug was empty or matches old auto-slug
        $('#cat-name').on('keyup', function () {
            $('#cat-slug').val(convertToSlug($(this).val()));
            checkSlug($('#cat-slug').val());
        });

        $('#cat-slug').on('keyup', function () {
            checkSlug($(this).val());
        });

        function convertToSlug(text) {
            return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-');
        }

        function checkSlug(slug) {
            if (slug.length > 0) {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                $.post({
                    url: '{{ route('admin.blog-category.check-slug') }}',
                    data: { slug: slug, id: '{{ $category->id }}' },
                    success: function (res) {
                        if (res.exists) {
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
    </script>
@endpush

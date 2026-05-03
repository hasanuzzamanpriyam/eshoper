@extends('layouts.back-end.app')

@section('title', 'Blog Category Setup')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/back-end/img/brand.png') }}" alt="">
                Blog Category Setup
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <!-- Add New Form -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Category</h5>
                    </div>
                    <div class="card-body" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog-category.store') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="title-color">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="cat-name" class="form-control" placeholder="Enter Category Name" required>
                            </div>
                            <div class="form-group">
                                <label for="slug" class="title-color">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="cat-slug" class="form-control" placeholder="Enter Slug" required>
                                <div id="slug-message" class="mt-1" style="font-size: 0.85rem;"></div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" class="btn btn-secondary px-4">Reset</button>
                                <button type="submit" class="btn btn--primary px-4">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="text-capitalize d-flex gap-1">
                                    Category List
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $categories->total() }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                            style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $categories->firstItem() + $key }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm" for="status-{{ $category->id }}">
                                                <input type="checkbox"
                                                    class="toggle-switch-input category-status"
                                                    id="status-{{ $category->id }}"
                                                    data-id="{{ $category->id }}"
                                                    {{ $category->status ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="Edit"
                                                    href="{{ route('admin.blog-category.edit', $category->id) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-danger btn-sm square-btn delete-category"
                                                    title="Delete" data-id="{{ $category->id }}">
                                                    <i class="tio-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4">
                            {!! $categories->links() !!}
                        </div>
                    </div>

                    @if (count($categories) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">No data to show</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Auto-generate slug from name
        $('#cat-name').on('keyup', function () {
            let name = $(this).val();
            $('#cat-slug').val(convertToSlug(name));
            checkSlug($('#cat-slug').val(), null);
        });

        $('#cat-slug').on('keyup', function () {
            checkSlug($(this).val(), null);
        });

        function convertToSlug(text) {
            return text.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-');
        }

        function checkSlug(slug, excludeId) {
            if (slug.length > 0) {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                let data = { slug: slug };
                if (excludeId) data.id = excludeId;
                $.post({
                    url: '{{ route('admin.blog-category.check-slug') }}',
                    data: data,
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

        // Status toggle
        $(document).on('change', '.category-status', function () {
            let id     = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.post({
                url: '{{ route('admin.blog-category.status') }}',
                data: { id: id, status: status },
                success: function () {
                    toastr.success('Status updated successfully!');
                }
            });
        });

        // Delete
        $(document).on('click', '.delete-category', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You will not be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                    $.ajax({
                        url: "{{ route('admin.blog-category.delete') }}",
                        method: 'POST',
                        data: { id: id },
                        success: function () {
                            toastr.success('Category deleted successfully');
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush

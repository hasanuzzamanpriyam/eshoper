@extends('layouts.back-end.app')

@section('title', 'Add New Blog')

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
                Blog Setup
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog.add-new') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="heading" class="title-color">Heading<span class="text-danger">*</span></label>
                                        <input type="text" name="heading" class="form-control" id="heading" placeholder="Enter Heading" required>
                                    </div>
                                    {{-- Add Slug Field --}}
                                    <div class="form-group">
                                        <label for="slug" class="title-color">Slug<span class="text-danger">*</span></label>
                                        <input type="text" name="slug" id="slug" class="form-control" placeholder="Enter Slug" required>
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
                                            src="{{ asset('assets/back-end/img/400x400/img2.jpg') }}" alt="blog image" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="title-color">Description<span class="text-danger">*</span></label>
                                        <textarea name="description" class="textarea editor-textarea" id="editor" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset" class="btn btn-secondary px-4">Reset</button>
                                <button type="submit" class="btn btn--primary px-4">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="text-capitalize d-flex gap-1">
                                    Blog List
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $blogs->total() }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Heading</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $key => $blog)
                                    <tr>
                                        <td>{{ $blogs->firstItem() + $key }}</td>
                                        <td>
                                            <img class="rounded" width="64"
                                                onerror="this.src='{{ asset('assets/back-end/img/160x160/img2.jpg') }}'"
                                                src="{{ asset('storage/blog') }}/{{ $blog['image'] }}"
                                                alt="">
                                        </td>
                                        <td>{{ $blog['heading'] }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="View"
                                                    href="{{ route('admin.blog.view', [$blog['slug']]) }}">
                                                    <i class="tio-invisible"></i>
                                                </a>
                                                <a class="btn btn-outline-info btn-sm square-btn" title="Edit"
                                                    href="{{ route('admin.blog.edit', [$blog['id']]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete square-btn" title="Delete"
                                                    id="{{ $blog['id'] }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4">
                            {!! $blogs->links() !!}
                        </div>
                    </div>

                    @if (count($blogs) == 0)
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

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You will not be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.blog.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success('Blog deleted successfully');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
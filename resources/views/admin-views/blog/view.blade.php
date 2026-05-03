@extends('layouts.back-end.app')

@section('title', 'View Blog')

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/back-end/img/brand.png') }}" alt="">
                Blog Details
            </h2>
            <div class="ml-auto">
                <a href="{{ route('admin.blog.add-new') }}" class="btn btn--primary">
                    <i class="tio-back-ui"></i> Back
                </a>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3>{{ $blog['heading'] }}</h3>
                        <p class="text-muted"><strong>Slug:</strong> {{ $blog['slug'] }}</p>
                        @if($blog['image'])
                            <div class="mb-4 text-center">
                                <img class="upload-img-view" style="max-width: 400px; height: auto;" src="{{ asset('storage/blog') }}/{{ $blog['image'] }}" alt="blog image" onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'" />
                            </div>
                        @endif
                        <hr>
                        <div class="mt-4">
                            {!! $blog['description'] !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

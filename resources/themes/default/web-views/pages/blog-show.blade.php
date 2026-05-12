@extends('layouts.front-end.app')

@section('title', $blog->heading)

@push('css_or_js')
    <meta property="og:image" content="{{ $blog->image ? asset('storage/blog/' . $blog->image) : '' }}"/>
    <meta property="og:title" content="{{ $blog->heading }}"/>
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($blog->description), 160) }}">

    <style>
        /* ===== Blog Detail Styles ===== */
        .blog-detail-header {
            background: linear-gradient(135deg, {{ $web_config['primary_color'] }}22 0%, {{ $web_config['secondary_color'] ?? $web_config['primary_color'] }}11 100%);
            border-bottom: 1px solid {{ $web_config['primary_color'] }}22;
            padding: 32px 0 20px;
            margin-bottom: 32px;
        }
        .breadcrumb-blog {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 10px;
        }
        .breadcrumb-blog a {
            color: {{ $web_config['primary_color'] }};
            text-decoration: none;
        }
        .blog-detail-title {
            font-size: 1.85rem;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1.35;
            margin: 0;
        }
        .blog-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 0.82rem;
            color: #888;
            margin-top: 14px;
        }
        .blog-detail-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ===== Main Card ===== */
        .blog-detail-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .blog-detail-hero {
            display: block;
            margin: 0 auto;
            width: 100% !important;
            max-height: 300px !important;
            object-fit: cover;
        }
        .blog-detail-body {
            padding: 30px;
        }
        .blog-detail-content {
            font-size: 1rem;
            line-height: 1.85;
            color: #333;
        }
        .blog-detail-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 12px 0;
        }

        /* ===== Sidebar ===== */
        .blog-sidebar {
            /* position: sticky;
            top: 100px; */
            height: fit-content;
        }
        .blog-sidebar-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 22px;
            margin-bottom: 24px;
        }
        .sidebar-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            padding-bottom: 12px;
            border-bottom: 2px solid {{ $web_config['primary_color'] }};
            margin-bottom: 16px;
        }
        .recent-post-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
            text-decoration: none;
            color: inherit;
            transition: color 0.18s;
        }
        .recent-post-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .recent-post-item:hover {
            text-decoration: none;
            color: {{ $web_config['primary_color'] }};
        }
        .recent-post-img {
            width: 64px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .recent-post-info h6 {
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.4;
            margin: 0 0 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #1a1a2e;
        }
        .recent-post-item:hover .recent-post-info h6 {
            color: {{ $web_config['primary_color'] }};
        }
        .recent-post-time {
            font-size: 0.75rem;
            color: #999;
        }

        /* ===== Back btn ===== */
        .btn-blog-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: {{ $web_config['primary_color'] }};
            text-decoration: none;
            border: 1.5px solid {{ $web_config['primary_color'] }}44;
            border-radius: 8px;
            padding: 6px 14px;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .btn-blog-back:hover {
            background: {{ $web_config['primary_color'] }};
            color: #fff;
            border-color: {{ $web_config['primary_color'] }};
            text-decoration: none;
        }
    </style>
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="blog-detail-header">
        <div class="container">
            <div class="breadcrumb-blog">
                <a href="{{ route('home') }}">{{ translate('home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('blogs') }}">{{ translate('blogs') }}</a>
                <span class="mx-1">/</span>
                <span>{{ Str::limit($blog->heading, 48) }}</span>
            </div>
            <h1 class="blog-detail-title">{{ $blog->heading }}</h1>
            <div class="blog-detail-meta">
                <span class="text-primary font-weight-bold"><i class="fa fa-folder-open"></i> {{ $blog->category->name ?? translate('uncategorized') }}</span>
                <span class="mx-2">|</span>
                @if($blog->author_name)
                    <span><i class="fa fa-user"></i> {{ $blog->author_name }}</span>
                    <span class="mx-2">|</span>
                @endif
                <span><i class="fa fa-calendar"></i> {{ $blog->created_at->format('d M Y') }}</span>
                <span class="mx-2">|</span>
                <span><i class="fa fa-clock-o"></i> {{ $blog->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mb-5 rtl">
        <div class="row">

            {{-- Article --}}
            <div class="col-lg-8">
                <div class="blog-detail-card">
                    @if($blog->image)
                        <img class="blog-detail-hero"
                             src="{{ asset('storage/blog/' . $blog->image) }}"
                             onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                             alt="{{ $blog->heading }}">
                    @endif
                    <div class="blog-detail-body">
                        <div class="blog-detail-content">
                            {!! $blog->description !!}
                        </div>
                        <a class="btn-blog-back" href="{{ route('blogs') }}">
                            <i class="fa fa-arrow-left"></i> {{ translate('back_to_blogs') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 mt-4 mt-lg-0 blog-sidebar">
                <div class="blog-sidebar-card mb-2">
                    <div class="sidebar-title">{{ translate('recent_posts') }}</div>
                    @forelse($recentPosts as $recent)
                        <a class="recent-post-item" href="{{ route('blog.show', $recent->slug) }}">
                            <img class="recent-post-img"
                                 src="{{ $recent->image ? asset('storage/blog/' . $recent->image) : asset('assets/front-end/img/image-place-holder.png') }}"
                                 onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                 alt="{{ $recent->heading }}">
                            <div class="recent-post-info">
                                <h6>{{ $recent->heading }}</h6>
                                <span class="recent-post-time">{{ $recent->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted" style="font-size:0.85rem;">{{ translate('no_recent_posts') }}</p>
                    @endforelse
                </div>

                {{-- Blog Page Banner --}}
                @if($blog_page_banner)
                    <div class="mb-2 overflow-hidden" style="border-radius: 12px;">
                        <a href="{{ $blog_page_banner->url }}">
                            <img src="{{ asset('storage/banner/' . $blog_page_banner->photo) }}"
                                 class="w-100 d-block"
                                 style="border-radius: 12px; min-height: 300px; max-height: 500px; object-fit: cover;"
                                 onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                 alt="{{ $blog_page_banner->title }}">
                        </a>
                    </div>
                @endif

                {{-- Related Products Section --}}
                @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <div class="blog-sidebar-card mt-0 mb-2">
                    <div class="sidebar-title">{{ translate('related_products') }}</div>
                    @foreach($relatedProducts as $product)
                        <a class="recent-post-item" href="{{ route('product', $product->slug) }}">
                            <img class="recent-post-img"
                                 src="{{ \App\CPU\ProductManager::product_image_path('thumbnail') }}/{{ $product->thumbnail }}"
                                 onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                 alt="{{ $product->name }}">
                            <div class="recent-post-info">
                                <h6>{{ $product->name }}</h6>
                                <span class="recent-post-time text-primary font-weight-bold">
                                    {{ \App\CPU\Helpers::currency_converter(
                                        $product->unit_price - \App\CPU\Helpers::get_product_discount($product, $product->unit_price)
                                    ) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
            </div>

        </div>
    </div>

@endsection

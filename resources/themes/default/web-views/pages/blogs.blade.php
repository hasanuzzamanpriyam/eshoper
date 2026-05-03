@extends('layouts.front-end.app')

@section('title', translate('blogs'))

@push('css_or_js')
    <meta property="og:title" content="Blog - {{ $web_config['name']->value }}"/>
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:description" content="Read our latest blog posts and stay updated.">

    <style>
        /* ===== Blog Page Styles ===== */
        .blog-page-header {
            background: linear-gradient(135deg, {{ $web_config['primary_color'] }}22 0%, {{ $web_config['secondary_color'] ?? $web_config['primary_color'] }}11 100%);
            border-bottom: 1px solid {{ $web_config['primary_color'] }}22;
            padding: 36px 0 24px;
            margin-bottom: 32px;
        }
        .blog-page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }
        .blog-page-header p {
            color: #6c757d;
            margin: 6px 0 0;
            font-size: 0.95rem;
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

        /* ===== Blog Card ===== */
        .blog-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
        }
        .blog-card-img-wrap {
            position: relative;
            overflow: hidden;
            height: 200px;
        }
        .blog-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .blog-card:hover .blog-card-img-wrap img {
            transform: scale(1.06);
        }
        .blog-card-body {
            padding: 18px 20px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .blog-card-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.78rem;
            color: #888;
            margin-bottom: 10px;
        }
        .blog-card-meta i {
            font-size: 0.72rem;
        }
        .blog-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1.45;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .blog-card-excerpt {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        .blog-card-footer {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .blog-read-more {
            font-size: 0.83rem;
            font-weight: 600;
            color: {{ $web_config['primary_color'] }};
            text-decoration: none;
            transition: gap 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .blog-read-more:hover {
            color: {{ $web_config['primary_color'] }};
            gap: 8px;
            text-decoration: none;
        }

        /* ===== Featured Blog Card Styles ===== */
        .featured-blog-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1); /* Enhanced shadow for featured post */
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            background: #fff;
            display: flex;
            flex-direction: column;
            margin-bottom: 12px; /* Reduced space below the featured card */
            height: 300px; /* Reduced to 300px (25% less than 400px) */
        }
        .featured-blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.17);
        }
        .featured-blog-card .blog-card-img-wrap {
            height: 60%;
            width: 100%;
        }
        .featured-blog-card .blog-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .featured-blog-card .blog-card-body {
            height: 40%;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .featured-blog-card .blog-card-title {
            font-size: 1.5rem; /* Larger title for featured post */
            margin-bottom: 12px;
            -webkit-line-clamp: 3; /* Allow more lines for title */
        }
        .featured-blog-card .blog-card-excerpt {
            font-size: 1rem; /* Larger excerpt */
            -webkit-line-clamp: 4; /* Allow more lines for excerpt */
            flex: 1;
        }
        .featured-blog-card .blog-read-more {
            font-size: 0.9rem; /* Larger read more link */
        }
        .featured-blog-card .blog-card-meta {
            font-size: 0.85rem; /* Larger meta font size */
            gap: 15px;
            margin-bottom: 15px;
        }

        /* ===== Sidebar ===== */
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

        /* ===== Empty State ===== */
        .blog-empty {
            text-align: center;
            padding: 60px 0;
        }
        .blog-empty i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 16px;
            display: block;
        }
        .blog-empty p {
            color: #888;
            font-size: 1rem;
        }

        /* ===== Pagination ===== */
        .blog-pagination .page-link {
            color: {{ $web_config['primary_color'] }};
            border-radius: 6px !important;
            margin: 0 2px;
        }
        .blog-pagination .page-item.active .page-link {
            background-color: {{ $web_config['primary_color'] }};
            border-color: {{ $web_config['primary_color'] }};
            color: #fff;
        }
    </style>
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="blog-page-header">
        <div class="container">
            <div class="breadcrumb-blog">
                <a href="{{ route('home') }}">{{ translate('home') }}</a>
                <span class="mx-1">/</span>
                <span>{{ translate('blogs') }}</span>
            </div>
            <h1>{{ translate('blogs') }}</h1>
            <p>{{ translate('read_our_latest_articles_and_stay_updated') }}</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mb-5 rtl">
        <div class="row">

            {{-- Blog Cards (left/main column) --}}
            <div class="col-lg-8" style="padding-right: 15px;">
                @if($blogs->count())
                    {{-- Featured Blog Post --}}
                    @php
                        $featured_blog = $blogs->first();
                        $other_blogs = $blogs->skip(1);
                    @endphp

                    @if($featured_blog)
                        <div class="row mb-2"> {{-- Reduced margin below featured post --}}
                            <div class="col-12"> {{-- Full width for featured post --}}
                                <div class="featured-blog-card">
                                    <div class="blog-card-img-wrap">
                                        <img src="{{ $featured_blog->image ? asset('storage/blog/' . $featured_blog->image) : asset('assets/front-end/img/image-place-holder.png') }}"
                                             onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                             alt="{{ $featured_blog->heading }}">
                                    </div>
                                    <div class="blog-card-body">
                                        <div class="blog-card-meta">
                                            <span><i class="fa fa-calendar"></i> {{ $featured_blog->created_at->format('d M Y') }}</span>
                                        </div>
                                        <a href="{{ route('blog.show', $featured_blog->slug) }}" class="blog-card-title">{{ $featured_blog->heading }}</a>
         
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Other Blog Posts Grid --}}
                    @if ($other_blogs->count())
                        <div class="row g-4">
                            @foreach($other_blogs as $blog)
                                <div class="col-sm-6">
                                    <div class="blog-card">

                                        <div class="blog-card-img-wrap">
                                            <img src="{{ $blog->image ? asset('storage/blog/' . $blog->image) : asset('assets/front-end/img/image-place-holder.png') }}"
                                                 onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                                 alt="{{ $blog->heading }}">
                                        </div>
                                        <div class="blog-card-body">
                                            <div class="blog-card-meta">
                                                <span><i class="fa fa-calendar"></i> {{ $blog->created_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="blog-card-title">{{ $blog->heading }}</div>
                                            <p class="blog-card-excerpt">{{ Str::limit(strip_tags($blog->description), 120) }}</p>
                                            <div class="blog-card-footer">
                                                <a class="blog-read-more" href="{{ route('blog.show', $blog->slug) }}">
                                                    {{ translate('read_more') }} <i class="fa fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    {{-- Adjust pagination to consider the featured post if it takes up a full row --}}
                    <div class="d-flex justify-content-start mt-4 blog-pagination">
                        {{ $blogs->links() }}
                    </div>

                @else
                    <div class="blog-empty">
                        <i class="fa fa-file-text-o"></i>
                        <p>{{ translate('no_blogs_found') }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 mt-4 mt-lg-0">

                {{-- Recent Posts --}}
                <div class="blog-sidebar-card">
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

            </div>
        </div>
    </div>

@endsection

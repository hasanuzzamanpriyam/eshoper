@if($blogs->count())
    @php
        $featured_blog = $blogs->first();
        $other_blogs = $blogs->skip(1);
    @endphp

    @if($featured_blog)
        <div class="row mb-2">
            <div class="col-12">
                <div class="featured-blog-card">
                    <a href="{{ route('blog.show', $featured_blog->slug) }}" class="blog-card-img-wrap">
                        <img src="{{ $featured_blog->image ? asset('storage/blog/' . $featured_blog->image) : asset('assets/front-end/img/image-place-holder.png') }}"
                             onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                             alt="{{ $featured_blog->heading }}">
                    </a>
                    <div class="blog-card-body">
                        <a href="{{ route('blog.show', $featured_blog->slug) }}" class="blog-card-title">{{ $featured_blog->heading }}</a>
                        <div class="blog-card-meta">
                            <span class="text-primary font-weight-bold">{{ $featured_blog->category->name ?? translate('uncategorized') }}</span>
                            <span class="mx-2">|</span>
                            @if($featured_blog->author_name)
                                <span><i class="fa fa-user"></i> {{ $featured_blog->author_name }}</span>
                                <span class="mx-2">|</span>
                            @endif
                            <span><i class="fa fa-calendar"></i> {{ $featured_blog->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($other_blogs->count())
        <div class="row g-4">
            @foreach($other_blogs as $blog)
                <div class="col-sm-6">
                    <div class="blog-card">
                        <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-img-wrap">
                            <img src="{{ $blog->image ? asset('storage/blog/' . $blog->image) : asset('assets/front-end/img/image-place-holder.png') }}"
                                 onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                 alt="{{ $blog->heading }}">
                        </a>
                        <div class="blog-card-body">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-title">{{ $blog->heading }}</a>
                            <div class="blog-card-meta">
                                <span class="text-primary font-weight-bold">{{ $blog->category->name ?? translate('uncategorized') }}</span>
                                <span class="mx-2">|</span>
                                @if($blog->author_name)
                                    <span><i class="fa fa-user"></i> {{ $blog->author_name }}</span>
                                    <span class="mx-2">|</span>
                                @endif
                                <span><i class="fa fa-calendar"></i> {{ $blog->created_at->format('d M Y') }}</span>
                            </div>
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

    <div class="d-flex justify-content-start mt-4 blog-pagination">
        {{ $blogs->appends(request()->query())->links() }}
    </div>

@else
    <div class="blog-empty">
        <i class="fa fa-file-text-o"></i>
        <p>{{ translate('no_blogs_found') }}</p>
    </div>
@endif

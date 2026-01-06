<div class="search-result-list">
    @if($products->count() > 0)
        @foreach($products as $product)
            <a href="{{ route('product', $product->slug) }}"
                class="search-result-item d-flex align-items-center gap-3 p-2 border-bottom">
                <img src="{{ asset('storage/product/thumbnail/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                    class="rounded" style="width: 50px; height: 50px; object-fit: cover;"
                    onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'">
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-dark">{{ $product->name }}</h6>
                </div>
            </a>
        @endforeach
    @else
        <p class="text-center text-muted p-3">{{ translate('no_products_found') }}</p>
    @endif
</div>
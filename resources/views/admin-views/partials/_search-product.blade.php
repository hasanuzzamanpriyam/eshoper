@foreach ($products as $key => $product)
    <div class="premium-product-item select-product-item d-flex gap-3 align-items-center" data-id="{{$product['id']}}">
        <div class="d-flex align-items-center">
            <input type="checkbox" class="product-checkbox" value="{{$product['id']}}">
        </div>
        <img width="60" height="60"
             onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
             src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
             alt="">
        <div class="info flex-grow-1">
            <h6 class="product-name mb-1 font-size-sm" style="font-size: 13px;">{{$product['name']}}</h6>
            <div class="d-flex flex-wrap gap-2">
                <span class="custom-badge">{{translate('category')}}: {{isset($product->category) ? $product->category->name : translate('N/A') }}</span>
                <span class="custom-badge">{{translate('brand')}}: {{isset($product->brand) ? $product->brand->name : translate('N/A') }}</span>
            </div>
        </div>
    </div>
@endforeach

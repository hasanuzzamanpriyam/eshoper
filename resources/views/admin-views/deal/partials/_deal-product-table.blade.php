@foreach($deal_products as $k=>$product)
    <tr>
        <td>{{ $deal_products->firstitem() + $k }}</td>
        <td><a href="#" target="_blank" class="font-weight-semibold title-color hover-c1">{{$product['name']}}</a></td>
        <td>{{\App\CPU\BackEndHelper::usd_to_currency($product['unit_price'])}}</td>
        <td>
            @if(isset($product->flash_deal_product) && $product->flash_deal_product->isNotEmpty())
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-{{ $product->flash_deal_product->first()->priority <= 3 ? 'danger' : ($product->flash_deal_product->first()->priority <= 6 ? 'warning' : 'secondary') }}">
                        {{ $product->flash_deal_product->first()->priority }}
                    </span>
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm edit-priority"
                       data-product-id="{{$product['id']}}"
                       data-deal-id="{{$deal->id}}"
                       data-current-priority="{{$product->flash_deal_product->first()->priority}}"
                       title="{{ translate('edit_priority') }}">
                        <i class="tio-edit"></i>
                    </a>
                </div>
            @else
                <span class="badge badge-secondary">10</span>
            @endif
        </td>

        <td>
            <div class="d-flex justify-content-center">
                <a  title="{{ translate ('delete')}}"
                    class="btn btn-outline-danger btn-sm delete"
                    id="{{$product['id']}}">
                    <i class="tio-delete"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach

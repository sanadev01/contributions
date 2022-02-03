<div>
    <div class="col-12 row mb-5">
        <div class="form-group row col-4">
            <label class="col-2 text-right">@lang('inventory.Scan SKU')</label>
            <input type="text" class="form-control col-8" wire:model.debounce.500ms="search">
            @error('search')
                <span class="text-danger ml-5">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group row col-4">
            @if ($scannedProducts)
                <h1 class="text-primary">No of Products : {{ $totalProducts }}</h1>
            @endif
        </div>
    </div>
    @if ($productError)
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-danger" role="alert">
                        {{ $productError }}
                    </div>
                </div>
            </div>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>user</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>SKU</th>
        </tr>
        @forelse ($scannedProducts as $product)
            <tr>
                <td>{{ $product['user']['name'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['quantity'] }}</td>
                <td>{{ $product['total_price'] }}</td>
                <td>{{ $product['sku'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Scan SKU of product please!</td>
            </tr>
        @endforelse
    </table>

    @if ($scannedProducts)
        <div class="col-12 row mb-5">
            <button class="btn btn-primary" type="button" wire:click="placeOrder()">Place Order</button>
        </div>
    @endif
</div>

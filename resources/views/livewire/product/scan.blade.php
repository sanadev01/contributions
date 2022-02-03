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
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                <h3>{{ $productError }}</h3>
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
            <th>Action</th>
        </tr>
        @forelse ($scannedProducts as $product)
            <tr>
                <td>{{ $product['user']['name'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['quantity'] }}</td>
                <td>{{ $product['total_price'] }}</td>
                <td>{{ $product['sku'] }}</td>
                <td>
                    <button wire:click="removeProduct({{ $product['id'] }})" class="btn btn-danger">Remove</button>
                </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    <h3>Scan SKU of product please!</h3>
                </td>
            </tr>
        @endforelse
    </table>

    @if ($scannedProducts)
        <div class="col-12 row mb-5">
            <button class="btn btn-primary" type="button" wire:click="placeOrder()">Place Order</button>
        </div>
    @endif
</div>

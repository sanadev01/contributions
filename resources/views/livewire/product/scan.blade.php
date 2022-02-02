<div>
    <div class="col-12 row mb-5">
        <div class="form-group row col-4">
            <label class="col-2 text-right">Scan Product</label>
            <input type="text" class="form-control col-8" wire:model.debounce.500ms="search">
            @error('search')
                <span class="text-danger ml-5">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group row col-4">

        </div>

        <div class="col-4 d-flex justify-content-end">

        </div>

        <div class="row col-12 d-flex justify-content-end">

        </div>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>user</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>SKU</th>
            <th>Action</th>
        </tr>
        @if ($scannedProducts)
            @foreach ($scannedProducts as $product)
                <tr>
                    <td>{{ $product->user->name }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>
                        <button wire:click="removeProduct({{ $product->id }})" class="btn btn-danger">Remove</button>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
</div>

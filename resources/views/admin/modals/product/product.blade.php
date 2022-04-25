
    <style>
        .modal-lg, .modal-xl {
            max-width: 70% !important;
        }
    </style>

<section class="card invoice-page">
    <div id="invoice-template" class="card-body">
        <div id="invoice-customer-details" class="pt-2 d-flex w-100 justify-content-between">
            <div class="text-left w-50">
                <h5>Product Details</h5>
            </div>
        </div>
        
        <div id="invoice-items-details" class="pt-1 invoice-items-table">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Product Name</th>
                                <th>price</th>
                                <th>SKU</th>
                                <th>Status</th>
                            </tr>
                            <tr>
                                <td class="danger">{{ $product->created_at->format('d M Y') }}</td>
                                <td class="danger">{{ $product->user->name }}</td>
                                <td class="danger">{{ $product->name }}</td>
                                <td class="danger">{{ $product->price }} </td>
                                <td class="danger">{{ $product->sku }} </td>
                                <td class="danger">{{$product->status}} </td>
                            </tr>
                            <tr>
                                <td class="danger"></td>
                            </tr>
                            <tr>
                                <th>Order</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Manufacturer</th>
                                <th>Barcode</th>
                                <th>Description</th>
                            </tr>
                           
                            <tr>
                                <td class="danger">{{ $product->order }}</td>
                                <td class="danger">{{ $product->category }}</td>
                                <td class="danger">{{ $product->brand }}</td>
                                <td class="danger">{{ $product->manufacturer }} </td>
                                <td class="danger">{{ $product->barcode }} </td>
                                <td class="danger">{{ $product->description}} </td>
                            </tr>
                            <tr>
                                <td class="danger"></td>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <th>Item</th>
                                <th>Lot</th>
                                <th>Unit</th>
                                <th>Case</th>
                                <th>Inventory Value</th>
                            </tr>
                            <tr>
                                <td class="danger">{{ $product->quantity }}</td>
                                <td class="danger">{{ $product->item }}</td>
                                <td class="danger">{{ $product->lot }}</td>
                                <td class="danger">{{ $product->unit }} </td>
                                <td class="danger">{{ $product->case }} </td>
                                <td class="danger">{{ $product->inventory_value }} </td>
                            </tr>
                            <tr>
                                <td class="danger"></td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <th>Min Quantity</th>
                                <th>Max Quantity</th>
                                <th>Discontinued</th>
                                <th>Store Day</th>
                                <th>Location</th>
                            </tr>
                            <tr>
                                <td class="danger">{{ $product->weight }}</td>
                                <td class="danger">{{ $product->min_quantity }}</td>
                                <td class="danger">{{ $product->max_quantity }}</td>
                                <td class="danger">{{ $product->discontinued }}</td>
                                <td class="danger">{{ Carbon\Carbon::parse($product->created_at)->diffInDays(Carbon\Carbon::now()) }} </td>
                                <td class="danger">{{ $product->location }} </td>
                            </tr>                                 
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
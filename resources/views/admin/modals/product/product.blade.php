<section class="card invoice-page">
    {{-- <div class="col-12 row no-print justify-content-end">
        <button class="btn btn-primary btn-print mb-1 mb-md-0 waves-effect waves-light" onclick="print('.invoice-page');"> <i class="feather icon-file-text"></i> Print</button>
    </div> --}}
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
                                <td>{{ $product->created_at->format('d M Y') }}</td>
                                <td>{{ $product->user->name }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->price }} </td>
                                <td>{{ $product->sku }} </td>
                                <td>{{$product->status}} </td>
                            </tr>                                
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>{{ $product->sh_code }}</td>
                                <td>{{ $product->merchant }}</td>
                                <td>{{ $product->carrier }}</td>
                                <td>{{ $product->tracking_id }}</td>
                                <td>{{ $product->warehouse_number }} </td>
                                <td>{{ $product->tracking_id }} </td>
                            </tr>                                
                            <tr>
                                <th>@lang('orders.invoice.length')</th>
                                <th>@lang('orders.invoice.width')</th>
                                <th>@lang('orders.invoice.height')</th>
                                <th>@lang('orders.invoice.weight')</th>
                                <th>@lang('orders.invoice.unit')</th>
                                <th>Invoce</th>
                            </tr>
                            <tr>
                                <td>{{ $product->length }} in</td>
                                <td>{{ $product->width }} in</td>
                                <td>{{ $product->height }} in</td>
                                <td>{{ $product->weight }} {{ $product->measurement_unit }}</td>
                                <td>{{ $product->measurement_unit }} </td>
                                <td>
                                    @if ( $product->fileInvoice )
                                        <div class="mt-2">
                                            <a target="_blank" href="{{ $product->fileInvoice->getPath() }}" class="m-2"> {{ $product->fileInvoice->name }} </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>                                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- <div id="invoice-items-details" class="pt-1 invoice-items-table border-success border-2">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="5">
                                    <h4>@lang('orders.invoice.Order Items')</h4>
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('orders.invoice.ShCode')</th>
                                <th>@lang('orders.invoice.Description')</th>
                                <th>@lang('orders.invoice.Quantity')</th>
                                <th>@lang('orders.invoice.Unit Value')</th>
                                <th>@lang('orders.invoice.Total')</th>
                                <th>@lang('orders.invoice.Battery/Perfume/Flameable')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->sh_code }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->value,2) }} USD</td>
                                    <td>{{ number_format($item->value * $item->quantity,2) }}</td>
                                    <td>
                                        {{ $item->contains_battery ? 'battery' : '' }}
                                        {{ $item->contains_perfume ? 'perfume' : '' }}
                                        {{ $item->contains_flammable_liquid ? 'flameable' : '' }}
                                    </td>
                                </tr>  
                            @endforeach
                            <tr class="border-top-light">
                                <td colspan="4" class="text-center h4">@lang('orders.invoice.Order Value')</td>
                                <td class="h4">
                                    {{ number_format($order->items()->sum(\DB::raw('quantity * value')),2) }} USD
                                </td>
                                <td></td>
                            </tr>                             
                            <tr class="border-top-light">
                                <td colspan="4" class="text-center h4">@lang('orders.invoice.Freight Declared to Custom')</td>
                                <td class="h4">
                                    {{ number_format($order->user_declared_freight,2) }} USD
                                </td>
                                <td></td>
                            </tr>                             
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="invoice-total-details" class="invoice-total-table">
            <div class="row">
                <div class="col-7 offset-5">
                    <div class="table-responsive-md">
                        <table class="table table-bordered">
                            <tbody>
                                
                                <tr>
                                    <th>@lang('orders.invoice.Shipping')</th>
                                    <td>{{ number_format($order->shipping_value,2) }} USD</td>
                                </tr>
                                <tr>
                                    <th>@lang('orders.invoice.Additional Services')</th>
                                    <td>
                                        {{ number_format($order->services()->sum('price'),2) }} USD
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('orders.invoice.Dangrous Items Cost')</th>
                                    <td>
                                        {{ number_format($order->dangrous_goods,2) }} USD
                                    </td>
                                </tr>
                                @if ( $order->isConsolidated() )
                                    <tr>
                                        <th>@lang('orders.invoice.consolidation')</th>
                                        <td>
                                            {{ number_format($order->consolidation,2) }} USD
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>@lang('orders.invoice.TOTAL')</th>
                                    <td> {{ number_format($order->gross_total,2) }} USD</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</section>
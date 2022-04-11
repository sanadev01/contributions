<section class="card invoice-page">
    <div class="col-12 row no-print justify-content-end">
        <button class="btn btn-primary btn-print mb-1 mb-md-0 waves-effect waves-light" onclick="print('.invoice-page');"> <i class="feather icon-file-text"></i> Print</button>
    </div>
    <div id="invoice-template" class="card-body">
        <div id="invoice-company-details" class="row">
            <div class="col-sm-6 col-12 text-left pt-1">
                <div class="media pt-1">
                    <img src="https://app.homedeliverybr.com/images/hd-logo.png" alt="Home Deliverybr">
                </div>
            </div>
        </div>
        <div id="invoice-items-details" class="pt-1 invoice-items-table border-success border-2 mt-5">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="5">
                                    <h4>Order Products</h4>
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('orders.invoice.ShCode')</th>
                                <th>SKU</th>
                                <th>Location</th>
                                <th>@lang('orders.invoice.Description')</th>
                                <th>Weight</th>
                                <th>@lang('orders.invoice.Quantity')</th>
                                <th>@lang('orders.invoice.Unit Value')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->sh_code }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->location }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>
                                        {{ $product->getWeightInKg() * $order->items()->where([
                                            ['description', $product->description],
                                            ['sh_code', $product->sh_code]
                                        ])->first()->quantity }}
                                    </td>
                                    <td>
                                        {{ $order->items()->where([
                                        ['description', $product->description],
                                        ['sh_code', $product->sh_code]
                                        ])->first()->quantity }}
                                    </td>
                                    <td>{{ number_format($product->price,2) }} USD</td>
                                </tr>  
                            @endforeach
                            <tr class="border-top-light">
                                <td colspan="3" class="text-center h4">Total Weight</td>
                                <td class="h4">{{ $order->getWeight('kg') }} kg</td>
                            </tr>
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
                                    @if (number_format($order->user_declared_freight,2) == 0.01)
                                        0.00 USD
                                    @else
                                        {{ number_format($order->user_declared_freight,2) }} USD
                                    @endif
                                </td>
                                <td></td>
                            </tr>                             
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
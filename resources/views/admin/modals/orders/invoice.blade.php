<section class="card invoice-page">
    <div class="col-12 row justify-content-between">
        <div class="py-0 mt-0 pl-3">
            <p class="mb-0">Herco Freight Forwarding <br>
                2200 NW 129th Ave Suite#100 <br>
                Miami, FL 33182 DBA As 
            </p>
        </div>
        <div class="no-print">
          <button class="btn btn-primary btn-print mb-1 mb-md-0 waves-effect waves-light" onclick="print('.invoice-page');"> <i class="feather icon-file-text"></i> Print</button>
        </div>
    </div> 

    <div id="invoice-template" class="card-body pt-0">
        <!-- Invoice Company Details -->
        <div id="invoice-company-details" class="row">
            <div class="col-sm-6 col-12 text-left pt-1">
                <div class="media pt-1">
                    <img src="https://app.homedeliverybr.com/images/hd-logo.png" alt="Home Deliverybr">
                </div>
            </div>
            <div class="col-sm-6 col-12 text-right">
                <h1>@lang('orders.invoice.Invoice')</h1>
                <div class="invoice-details mt-2">
                    <h6>@lang('orders.invoice.INVOICE NO')</h6>
                    <p>{{ $order->warehouse_number }}</p>
                    <h6 class="mt-2">@lang('orders.invoice.INVOICE DATE')</h6>
                    @if($order->getPaymentInvoice())
                        <p>{{ $order->getPaymentInvoice()->updated_at->format('d M Y') }}</p>
                    @else
                        <p>{{ optional($order->order_date)->format('m/d/Y') }}</p>
                    @endif

                </div>
            </div>
        </div>
        <!--/ Invoice Company Details -->

        <!-- Invoice Recipient Details -->
        <div id="invoice-customer-details" class="pt-2 d-flex w-100 justify-content-between">
            <div class="text-left w-50">
                <h5>@lang('orders.invoice.Recipient')</h5>
                <div class="recipient-info my-2">
                    <p> {{ optional($order->recipient)->first_name }} {{ optional($order->recipient)->last_name }} </p>
                    <p>{{ optional($order->recipient)->address }} {{ optional($order->recipient)->address2 }} {{ optional($order->recipient)->street_no }}<br>
                        {{ optional($order->recipient)->city }}, {{ optional(optional($order->recipient)->state)->code }}, {{ optional($order->recipient)->zipcode }}<br>
                        {{ optional(optional($order->recipient)->country)->name }}<br>
                        <i class="feather icon-phone"></i> Ph#: {{ optional($order->recipient)->phone }}
                    </p>
                </div>
                <div class="recipient-contact pb-2">
                    <p>
                        <i class="feather icon-mail"></i>
                        {{ optional($order->recipient)->email }}
                    </p>
                    <p>
                        {{ optional($order->recipient)->tax_id }}
                    </p>
                </div>
            </div>
            <div class="text-righ justify-self-end">
                <h5>@lang('orders.invoice.Sender')</h5>
                <div class="company-info my-2">
                    {{ $order->sender_first_name }} {{ $order->sender_last_name }} <br>
                    @if($order->sender_city)
                        {{ optional($order)->sender_address }}<br> 
                        {{ optional($order)->sender_city }}, {{ optional($order)->sender_state }}, {{ optional($order)->sender_zipcode }}<br>
                        {{ optional($order)->sender_country}}<br>
                        Ph#: {{ optional($order)->sender_phone }}
                    @else
                        2200 NW, 129th Ave - Suite # 100<br> Miami, FL, 33182<br>United States<br>Ph#: +13058885191
                    @endif
                </div>
                <div class="recipient-contact pb-2">
                    <p>
                        <i class="feather icon-mail"></i>
                        {{ $order->sender_email }}
                    </p>
                    <p>
                        {{ $order->sender_phone }}
                    </p>
                </div>
            </div>
        </div>
        <!--/ Invoice Recipient Details -->

        <!-- Invoice Shipment Details -->
        <div id="invoice-items-details" class="pt-1 invoice-items-table">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>@lang('orders.invoice.merchant')</th>
                                <th>@lang('orders.invoice.carrier')</th>
                                <th>@lang('orders.invoice.carrier_tracking')</th>
                                <th>@lang('orders.invoice.whr_number')</th>
                                <th>@lang('orders.invoice.customer_reference')</th>
                                <th>@lang('orders.invoice.tracking_code')</th>
                            </tr>
                            <tr>
                                <td>{{ $order->merchant }}</td>
                                <td>{{ $order->carrier }}</td>
                                <td>{{ $order->tracking_id }}</td>
                                <td>{{ $order->warehouse_number }} </td>
                                <td>{{ $order->customer_reference }} </td>
                                <td>{{ $order->corrios_tracking_code }} </td>
                            </tr>
                            <tr>
                                <th>@lang('orders.invoice.length')</th>
                                <th>@lang('orders.invoice.width')</th>
                                <th>@lang('orders.invoice.height')</th>
                                <th>@lang('orders.invoice.weight')</th>
                                <th @if (!$appliedVolumeWeight) colspan="2" @endif>@lang('orders.invoice.unit')</th>
                                @if ($appliedVolumeWeight)
                                <th>@lang('orders.invoice.Discount')</th>
                                @endif
                            </tr>
                            <tr>
                                <td>{{ $order->length }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                <td>{{ $order->width }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                <td>{{ $order->height }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                <td>
                                    Weight: {{ round($order->getOriginalWeight('kg'),2) }} Kg ( {{ round($order->getOriginalWeight('lbs'),2) }} lbs ) <br>
                                    Vol. Weight: {{ round($order->getWeight('kg'),2) }} Kg ( {{ round($order->getWeight('lbs'),2) }} lbs ) <br>
                                    @if ($appliedVolumeWeight && !$order->sender_city)
                                        Applied Weight:
                                        @if($order->measurement_unit == 'kg/cm')
                                            {{ round($appliedVolumeWeight,2) }} Kg ( {{ round($appliedVolumeWeight * 2.205, 2) }} lbs )
                                        @else
                                            {{ round($appliedVolumeWeight / 2.205, 2) }} Kg ( {{ round($appliedVolumeWeight,2) }} lbs )
                                        @endif
                                    @endif
                                </td>
                                <td @if (!$appliedVolumeWeight) colspan="2" @endif>{{ $order->measurement_unit }} </td>
                                @if ($appliedVolumeWeight)
                                <td>
                                    Actual Rate <span class="text-primary font-weight-bold">${{ number_format($order->shipping_value + $order->discountCost(), 2) }}</span> to {{ round($order->getWeight('kg'),2) }} Kg<br>
                                    Applied Rate <span class="text-primary font-weight-bold">${{ number_format($order->shipping_value, 2) }}</span> to {{ $order->measurement_unit == 'kg/cm'? round($appliedVolumeWeight,2):round($appliedVolumeWeight / 2.205, 2) }} Kg <br>
                                    @if($order->shipping_value + $order->discountCost() - $order->shipping_value > 0)
                                        Difference <span class="text-primary font-weight-bold">${{ $order->discountCost() }} </span>Saving
                                    @endif
                                </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoice Items Details -->
        <div id="invoice-items-details" class="pt-1 invoice-items-table">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><h4>@lang('orders.invoice.Service')</h4></th>
                                <th>@lang('orders.invoice.Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $order->shipping_service_name }}</td>
                                <td>
                                    @if($order->sender_city)
                                        {{ number_format($order->gross_total,2) }}
                                    @else
                                        {{ number_format($order->shipping_value,2) }}
                                    @endif
                                    USD
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="invoice-items-details" class="pt-1 invoice-items-table">
            <div class="row">
                <div class="table-responsive-md col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><h4>@lang('orders.invoice.Additional Services')</h4></th>
                                <th>@lang('orders.invoice.Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ number_format($service->price,2) }} USD</td>
                                </tr>
                            @endforeach
                            @if($order->tax_and_duty>0)
                             <tr>
                                <td>
                                    Taxes & Duties
                                </td>
                                <td>
                                    {{$order->tax_and_duty}}
                                </td>
                             </tr>
                            @endif

                            <tr class="border-top-light">
                                <td class="text-center h4">@lang('orders.invoice.Total')</td>
                                <td class="h4">{{ number_format($services->sum('price')+$order->tax_and_duty,2) }} USD</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="invoice-items-details" class="pt-1 invoice-items-table border-success border-2">
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
        <div id="invoice-total-details" class="invoice-total-table">
            <div class="row">
                <div class="col-7 offset-5">
                    <div class="table-responsive-md">
                        <table class="table table-bordered">
                            <tbody>

                                <tr>
                                    <th>@lang('orders.invoice.Shipping')</th>
                                    <td>
                                        @if($order->sender_city)
                                            {{ number_format($order->gross_total,2) }}
                                        @else
                                            {{ number_format($order->shipping_value,2) }}
                                        @endif
                                        USD
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('orders.invoice.Additional Services')</th>
                                    <td>
                                        {{ number_format($services->sum('price')+$order->tax_and_duty,2) }} USD
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
                                    <td> {{ number_format($order->gross_total+$order->tax_and_duty,2) }} USD</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

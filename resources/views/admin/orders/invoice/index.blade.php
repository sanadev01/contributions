@extends('admin.orders.layouts.wizard')

@section('wizard-form')
    <section class="invoice-print mb-1">
        <div wire:id="MhlECvHN71T5Xdylc5Vi">
      
            <div class="row">
                <fieldset class="col-12 col-md-5 mb-1 mb-md-0">

                </fieldset>
                <div class="col-12 col-md-7 d-flex flex-column flex-md-row justify-content-end">
                    <button class="btn btn-primary btn-print mb-1 mb-md-0 waves-effect waves-light" onclick="window.print();"> <i class="feather icon-file-text"></i> Print</button>
                </div>
            </div>
        </div>
    </section>
    <div id="invoice-wrapper" class="wizard print">
        <!-- invoice functionality end -->
        <!-- invoice page --> 
        <section class="card invoice-page">
            <div id="invoice-template" class="card-body">
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
                            <h6>@lang('orders.invoice.INVOICE NO.')</h6>
                            <p>{{ $order->warehouse_number }}</p>
                            <h6 class="mt-2">@lang('orders.invoice.INVOICE DATE')</h6>
                            <p>{{ now()->format('d M Y') }}</p>
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
                            <p>{{ optional($order->recipient)->address }} {{ optional($order->recipient)->address2 }}<br> 
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
                            2200 NW, 129th Ave – Suite # 100<br> Miami, FL, 33182<br>United States<br>Ph#: +13058885191
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
                            <table class="table table-borderless">
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
                                        <th colspan="2">@lang('orders.invoice.unit')</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $order->length }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                        <td>{{ $order->width }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                        <td>{{ $order->height }} {{ $order->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                                        <td>{{ $order->getWeight('lbs') }} lbs ( {{ $order->getWeight('kg') }} kg ) </td>
                                        <td colspan="2">{{ $order->measurement_unit }} </td>
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
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th><h4>@lang('orders.invoice.Service')</h4></th>
                                        <th>@lang('orders.invoice.Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $order->shipping_service_name }}</td>
                                        <td>{{ $order->shipping_value }} USD</td>
                                    </tr>                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="invoice-items-details" class="pt-1 invoice-items-table">
                    <div class="row">
                        <div class="table-responsive-md col-12">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th><h4>@lang('orders.invoice.Additional Services')</h4></th>
                                        <th>@lang('orders.invoice.Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->services as $service)
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ $service->price }} USD</td>
                                        </tr>  
                                    @endforeach   
                                    <tr class="border-top-light">
                                        <td class="text-center h4">@lang('orders.invoice.Total')</td>
                                        <td class="h4">{{ $order->services()->sum('price') }} USD</td>
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="invoice-items-details" class="pt-1 invoice-items-table">
                    <div class="row">
                        <div class="table-responsive-md col-12">
                            <table class="table table-borderless">
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
                                            <td>{{ $item->value }} USD</td>
                                            <td>{{ $item->value * $item->quantity }}</td>
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
                                            {{ $order->items()->sum(\DB::raw('quantity * value')) }} USD
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
                                <table class="table table-borderless">
                                    <tbody>
                                        
                                        <tr>
                                            <th>@lang('orders.invoice.Shipping')</th>
                                            <td>{{ round($order->shipping_value,2) }} USD</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('orders.invoice.Additional Services')</th>
                                            <td>
                                                {{ round($order->services()->sum('price'),2) }} USD
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('orders.invoice.Dangrous Items Cost')</th>
                                            <td>
                                                {{ round($order->dangrous_goods,2) }} USD
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('orders.invoice.TOTAL')</th>
                                            <td> {{ round($order->gross_total,2) }} USD</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="actions clearfix no-print">
            <ul role="menu" aria-label="Pagination">
                @if ( !$order->isPaid() )
                    <li class="disabled" aria-disabled="true">
                        <a href="{{ route('admin.orders.order-details.index',$order) }}" role="menuitem">@lang('orders.invoice.Previous')</a>
                    </li>
                    <li aria-hidden="false" aria-disabled="false">
                        <a href="{{ route('admin.payment-invoices.orders.index',['order'=>$order]) }}" class="btn btn-primary">Pay Order Now</a>
                    </li>
                @else
                <li aria-hidden="false" aria-disabled="false">
                    <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">Back To List</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
@endsection

@extends('layouts.master')

@section('page')
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
                            <p>{{ $invoice->uuid }}</p>
                            <h6 class="mt-2">@lang('orders.invoice.INVOICE DATE')</h6>
                            <p>{{ optional($invoice->created_at)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                <!--/ Invoice Company Details -->

                <!-- Invoice Recipient Details -->
                <div id="invoice-customer-details" class="pt-2 d-flex w-100 justify-content-between">
                    <div class="text-left w-50">
                        <h5>@lang('orders.invoice.Recipient')</h5>
                        <div class="recipient-info my-2">
                            <p> {{ optional($invoice->user)->first_name }} {{ optional($invoice->user)->last_name }} </p>
                            <p>{{ optional($invoice->user)->address }} {{ optional($invoice->user)->address2 }}<br> 
                                {{ optional($invoice->user)->city }}, {{ optional(optional($invoice->user)->state)->code }}, {{ optional($invoice->user)->zipcode }}<br>
                                {{ optional(optional($invoice->user)->country)->name }}<br>
                                <i class="feather icon-phone"></i> Ph#: {{ optional($invoice->user)->phone }}
                        </p>
                        </div>
                        <div class="recipient-contact pb-2">
                            <p>
                                <i class="feather icon-mail"></i>
                                {{ optional($invoice->user)->email }}
                            </p>
                            <p>
                                {{ optional($invoice->user)->tax_id }}
                            </p>
                        </div>
                    </div>
                    <div class="text-righ justify-self-end">
                        <h5>@lang('orders.invoice.Sender')</h5>
                        <div class="company-info my-2">
                            Homedelivery Br <br>
                            2200 NW, 129th Ave – Suite # 100<br> Miami, FL, 33182<br>United States<br>Ph#: +13058885191
                        </div>
                    </div>
                </div>
                <!--/ Invoice Recipient Details -->

                <!-- Invoice Items Details -->
                <div id="invoice-items-details" class="pt-1 invoice-items-table mt-5">
                    <div class="row">
                        <div class="table-responsive-md col-12">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Recipient</th>
                                        <th>Tracking #</th>
                                        <th>Warehouse#</th>
                                        <th>Customer Reference#</th>
                                        <th>Date</th>
                                        <th>@lang('orders.invoice.Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->orders as $order)
                                        <tr>
                                            <td>{{ $order->recipient->first_name }} {{ $order->recipient->last_name }}</td>
                                            <td>{{ $order->corrios_tracking_code }}</td>
                                            <td>{{ $order->warehouse_number }}</td>
                                            <td>{{ $order->customer_reference }}</td>
                                            <td>{{ optional($order->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $order->gross_total }}</td>
                                        </tr>  
                                    @endforeach   
                                    <tr class="border-top-light">
                                        <td class="text-center h4" colspan="5">@lang('orders.invoice.Total')</td>
                                        <td class="h4">{{ $invoice->orders()->sum('gross_total') }} USD</td>
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="no-print">
                    <div class="row justify-content-end">
                        <div class="col-12 col-md-7 text-right">
                            <button class="btn btn-info btn-lg mb-1 mb-md-0 waves-effect waves-light" onclick="window.print();"> <i class="feather icon-file-text"></i> Print</button>
                            <a href="{{ route('admin.payment-invoices.invoice.edit',$invoice) }}" class="btn btn-primary btn-lg mb-1 mb-md-0 waves-effect waves-light" > <i class="feather icon-edit"></i> Edit</a>
                            
                            @if (!$invoice->isPaid())
                                <a href="{{ route('admin.payment-invoices.invoice.checkout.index',$invoice) }}" class="btn btn-success btn-lg mb-1 mb-md-0 waves-effect waves-light" > <i class="feather icon-credit-card"></i> Checkout</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

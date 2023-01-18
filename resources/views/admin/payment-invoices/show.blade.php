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
                            <h6>@lang('orders.invoice.INVOICE NO')</h6>
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
                        
                    </div>
                    <div class="text-right justify-self-end">
                        <h5>@lang('invoice.Sender')</h5>
                        <div class="company-info my-2">
                            {{ optional($invoice->user)->name }} {{ optional($invoice->user)->last_name }} <br>
                            {{ optional($invoice->user)->email }} <br>
                            {{ optional($invoice->user)->phone }}
                        </div>
                    </div>
                </div>
                <!--/ Invoice Recipient Details -->

                <!-- Invoice Items Details  -->
                <div id="invoice-items-details" class="pt-1 invoice-items-table mt-5">
                    <div class="row">
                        <div class="table-responsive-md col-12">
                            <table class="table table-borderless" id="datatable">
                                <thead>
                                    <tr> 
                                        <th>@lang('invoice.Recipient')</th>
                                        <th>@lang('invoice.Tracking') #</th>
                                        <th>@lang('invoice.Warehouse')#</th>
                                        <th>@lang('invoice.Customer Reference')#</th>
                                        <th>@lang('invoice.Date')</th>
                                        @if ( auth()->user()->isAdmin() && $invoice->isPrePaid() )
                                            <th>
                                                Shipping
                                            </th>
                                            <th>
                                                Additional Services
                                            </th>
                                            <th>
                                                Conslidation
                                            </th>
                                            <th>
                                                Restricted Items
                                            </th>
                                        @endif
                                        <th>@lang('invoice.Amount')</th>
                                        @if ($invoice->differnceAmount())
                                            <th>@lang('invoice.Paid')</th>
                                            <th>@lang('invoice.Remaining')</th>
                                        @endif
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
                                            <td>@if($invoice->differnceAmount()) {{ $order->gross_total }}  @else {{ $order->shipping_value }} @endif USD</td>
                                            @if ( auth()->user()->isAdmin() && $invoice->isPrePaid() )
                                                <td>{{ $order->services()->sum( 'price' ) }} USD</td>
                                                <td>{{ $order->consolidation }} USD</td>
                                                <td>{{ $order->dangrous_goods??0 }} USD</td>
                                                <td>{{ $order->gross_total }} USD</td>
                                            @endif
                                            @if ($invoice->differnceAmount())
                                                <td>{{ $invoice->paid_amount }} USD</td>
                                                <td>{{ $invoice->differnceAmount() }} USD</td>
                                            @endif
                                        </tr>  
                                    @endforeach   
                                    <hr>
                                    <tr class="border-top-light">
                                        <td class="text-center h4" style="border-top: 1px solid !important;" colspan=" {{ !$invoice->isPrePaid()? '5' : '9' }}">@lang('orders.invoice.Total')</td>
                                        <td class="h4" style="border-top: 1px solid !important;">{{ ($invoice->differnceAmount()) ? round($invoice->differnceAmount(), 2) : round($invoice->orders()->sum('gross_total'),2) }} USD</td>
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="no-print">
                    <div class="row justify-content-end">
                        <div class="col-12 col-md-7 text-right">
                            <a class="btn btn-primary btn-lg" href="{{ route('admin.payment-invoices.index') }}">@lang('invoice.Back to List')</a>
                            <button class="btn btn-info btn-lg mb-1 mb-md-0 waves-effect waves-light" onclick="print('.invoice-page');"> <i class="feather icon-file-text"></i> @lang('invoice.Print')</button>
                            @if (!$invoice->isPrePaid())
                                <a href="{{ route('admin.payment-invoices.postpaid.export',$invoice) }}" class="btn btn-primary btn-lg mb-1 mb-md-0 waves-effect waves-light" > <i class="feather icon-file"></i> @lang('Export')</a>
                            @endif
                            @if (!$invoice->isPaid())
                                <a href="{{ route('admin.payment-invoices.invoice.edit',$invoice) }}" class="btn btn-primary btn-lg mb-1 mb-md-0 waves-effect waves-light" > <i class="feather icon-edit"></i> @lang('invoice.Edit')</a>
                                <a href="{{ route('admin.payment-invoices.invoice.checkout.index',$invoice) }}" class="btn btn-success btn-lg mb-1 mb-md-0 waves-effect waves-light" > <i class="feather icon-credit-card"></i> @lang('invoice.Checkout')</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
<script>
    $(document).ready( function () {
        $('#datatable').DataTable();
    } );
</script>
@endsection
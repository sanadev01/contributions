@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/wizard.css') }}">
@yield('wizard-css')
<style>
    body {
        background-color: #f6f5ff !important;
    } 
    input,
    select {
        height: 40px !important;
        border-radius: 15px 15px;
        resize: none;
        background-color: #f6f5ff !important;
    } 
    .filter-card {
        padding: 15px 25px;
        border: 1px solid rgba(46, 61, 73, 0.15);
        margin-top: 0;
        margin-bottom: 1.5rem;
        text-align: left;
        position: relative;
        background: #fff;
        box-shadow: 0px 0px 40px 1px rgba(120, 148, 171, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    } 
    #filter-card:hover {
        box-shadow: 2px 4px 12px 6px rgba(46, 61, 73, 0.2);
    }

    .filter-card input,
    .filter-card textarea {
        border-radius: 10px 10px;
        resize: none;
    }

    .filter-card h4 {
        margin: 0px !important;
    }
</style>
@endsection

@section('page')
<section>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center   ">
                <div class="mt-sm-5 mt-md-0 rounded rounded-lg ">
                    <h4>@lang('orders.order-details.Orders')</h4>
                    <p class="h4 text-danger pb-2 mt-4">WHR# {{ optional($order)->warehouse_number }}</p>
                </div>
            </div>
            <div class="filter-card " id="filter-card">
           
                <div>
                    <div class="card-body">
                        <div class="number-tab-steps wizard-circle wizard clearfix" role="application" id="steps-uid-0">
                            <div class="steps clearfix no-print">
                                <ul role="tablist">
                                    <li role="tab" class="first {{ in_array(request()->route()->getName(),['admin.orders.sender.index'])? 'current' : 'disabled' }}" aria-disabled="false" aria-selected="true">
                                        <a id="steps-uid-0-t-0" href="#" aria-controls="steps-uid-0-p-0">
                                            {{-- <span class="current-info audible">current step: </span> --}}
                                            <span class="step">1</span> @lang('orders.order-details.Sender')
                                        </a>
                                    </li>
                                    <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.recipient.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                        <a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1">
                                            <span class="step">2</span> @lang('orders.order-details.Recipient')
                                        </a>
                                    </li>
                                    <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.order-details.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                        <a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1">
                                            <span class="step">3</span> @lang('orders.order-details.Shipping & Items')
                                        </a>
                                    </li>
                                    @if (($order->user->hasRole('wholesale') && $order->user->insurance == false) || ($order->user->hasRole('retailer')))
                                    <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.services.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                        <a id="steps-uid-0-t-2" href="#" aria-controls="steps-uid-0-p-2">
                                            <span class="step">4</span> @lang('orders.order-details.Additional services')
                                        </a>
                                    </li>
                                    @endif
                                    <li role="tab" class="last {{ in_array(request()->route()->getName(),['admin.orders.order-invoice.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                        <a id="steps-uid-0-t-2" href="#" aria-controls="steps-uid-0-p-2">
                                            @if (($order->user->hasRole('wholesale') && $order->user->insurance == false) || ($order->user->hasRole('retailer')))
                                            <span class="step">5</span> @lang('orders.order-details.Invoice')
                                            @else
                                            <span class="step">4</span> @lang('orders.order-details.Invoice')
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @yield('wizard-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
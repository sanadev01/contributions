@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/wizard.css') }}">
    @yield('wizard-css')
@endsection

@section('page')
    <section >
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Orders</h4>
                        {{-- <a href="{{ route('admin.roles.create') }}" class="pull-right btn btn-primary"> Create Role </a> --}}
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p></p>
                            <div class="number-tab-steps wizard-circle wizard clearfix" role="application" id="steps-uid-0">
                                <div class="steps clearfix no-print">
                                    <ul role="tablist">
                                        <li role="tab" class="first {{ in_array(request()->route()->getName(),['admin.orders.sender.index'])? 'current' : 'disabled' }}" aria-disabled="false" aria-selected="true">
                                            <a id="steps-uid-0-t-0" href="#" aria-controls="steps-uid-0-p-0">
                                                {{-- <span class="current-info audible">current step: </span> --}}
                                                <span class="step">1</span> Sender
                                            </a>
                                        </li>
                                        <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.recipient.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                            <a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1">
                                                <span class="step">2</span> Recipient
                                            </a>
                                        </li>
                                        <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.services.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                            <a id="steps-uid-0-t-2" href="#" aria-controls="steps-uid-0-p-2">
                                                <span class="step">3</span> Additional services
                                            </a>
                                        </li>
                                        
                                        <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.orders.order-details.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                            <a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1">
                                                <span class="step">4</span> Shipping & Items
                                            </a>
                                        </li>
                                        <li role="tab" class="last {{ in_array(request()->route()->getName(),['admin.orders.order-invoice.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                            <a id="steps-uid-0-t-2" href="#" aria-controls="steps-uid-0-p-2">
                                                <span class="step">5</span> Invoice
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

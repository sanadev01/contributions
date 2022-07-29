@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header pr-1">
                        <div class="col-12 d-flex justify-content-end">
                        @section('title', __('invoice.Payment Invoices'))
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                            class="btn btn-primary mr-1 waves-effect waves-light"><i
                                class="feather icon-filter"></i></button>
                        <button onclick="toggleLogsSearch()" class="btn btn-primary waves-effect mr-1 waves-light">
                            <i class="feather icon-search"></i>
                        </button>
                        <a href="{{ route('admin.payment-invoices.orders.index') }}" class="btn btn-primary">
                            @lang('invoice.Create invoice')
                        </a>
                    </div>
                </div>
                <div class="card-content card-body">
                    <div class="table-responsive-md mt-1">
                        <livewire:payment-invoice.table />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

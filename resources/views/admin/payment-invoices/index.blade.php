@extends('layouts.master') 

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('invoice.Payment Invoices')
                        </h4>
                        <a href="{{ route('admin.payment-invoices.orders.index') }}" class="btn btn-primary">
                            @lang('invoice.Create invoice')
                        </a>
                    </div>
                    <div class="card-content card-body">
                        <div class="table-responsive-md mt-1" >
                            <livewire:payment-invoice.table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('orders.orders')</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <livewire:reports.order-report-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

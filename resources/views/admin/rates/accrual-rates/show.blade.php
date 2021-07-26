@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @lang('shipping-rates.shipping-rates')
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.accrual-rates.index') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Return to List')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        @livewire('accrual-rate.table', ['shippingService' => $service])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

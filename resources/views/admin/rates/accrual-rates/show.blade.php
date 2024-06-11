@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @lang('shipping-rates.accrual-rates')
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                        <div class="row col-md-6">
                            <div class="ml-auto">
                                <a href="{{ route('admin.rates.accrual-rates.index') }}" class="pull-right btn btn-primary ml-2">
                                    @lang('shipping-rates.Return to List')
                                </a>
                                <a href="{{ route('admin.rates.download-accrual-rates', $service) }}" class="pull-right btn btn-success">
                                    @lang('shipping-rates.Download')
                                </a>
                            </div>    
                        </div>
                            
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

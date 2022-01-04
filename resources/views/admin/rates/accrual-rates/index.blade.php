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
                            <a href="{{ route('admin.rates.accrual-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <livewire:accrual-rate.index-table />
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

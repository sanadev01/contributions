@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-1">
                        <div class="col-12 d-flex justify-content-end">
                        @section('title', __('shipping-rates.accrual-rates'))
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.accrual-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-content card-body">
                    <livewire:accrual-rate.index-table />
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

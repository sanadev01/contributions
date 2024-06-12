@extends('layouts.master')
@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">@lang('shipping-rates.USPS Rates')</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <livewire:accrual-rate.usps-accrual-rates />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
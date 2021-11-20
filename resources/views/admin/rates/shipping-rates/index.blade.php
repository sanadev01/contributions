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
                            <a href="{{ route('admin.rates.shipping-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive">
                            <tbody>
                                @foreach ($shippingRates as $ratesData)
                                    <tr>
                                        <th colspan="4"><h3>Service Name</h3></th><th colspan="100%"><h4>{{ optional($ratesData->shippingService)->name }}</h4></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4"><h3>Country</h3></th><th colspan="100%"><h4>{{ optional($ratesData->country)->name }}</h4></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4"><h3>Region</h3></th><th colspan="100%"><h4>{{ optional($ratesData->region)->name }}</h4></th>
                                    </tr>
                                    <tr>
                                        <th>@lang('shipping-rates.Weight')</th>
                                        @foreach($ratesData->data??[] as $rate)
                                            <td>
                                                {{ isset($rate['weight'])?$rate['weight']:0 }} g
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>@lang('shipping-rates.Rates') ($)</th>
                                        @foreach($ratesData->data??[] as $rate)
                                            <td>
                                                {{ isset($rate['leve'])?$rate['leve']:0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th colspan="100%">
                                            <hr>
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $shippingRates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

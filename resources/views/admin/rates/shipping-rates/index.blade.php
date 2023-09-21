@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-1">
                        <div class="col-12 d-flex justify-content-end">
                        @section('title', __('shipping-rates.shipping-rates'))
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.shipping-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-content card-body">
                    <table class="table table-bordered table-responsive-md">
                        <thead>
                            <tr>
                                <th>
                                    Service
                                </th>
                                <th>
                                    Country
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shippingRates as $rate)
                                <tr>
                                    <th>
                                        {{ optional($rate->shippingService)->name }}
                                    </th>
                                    <th>
                                        {{ optional($rate->country)->name }}
                                    </th>
                                    <th>
                                        @if(optional($rate->shippingService)->name == "PostNL")
                                            <a href="{{ route('admin.rates.country-rates', $rate->shippingService) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="feather icon-eye"></i> View Country Rates
                                            </a>
                                        @else
                                            <a class="btn btn-primary btn-sm" href="
                                                @if ($rate->region)
                                                    {{ route('admin.rates.region-rates', $rate->shippingService) }}
                                                @else
                                                    {{ route('admin.rates.view-shipping-rates', $rate) }}
                                                @endif
                                                ">
                                                <i class="feather icon-eye"></i> View
                                            </a>
                                            |
                                            <a href="{{ route('admin.rates.download-shipping-rates', $rate) }}" class="btn btn-success btn-sm">
                                                <i class="feather icon-download"></i> Download
                                            </a>
                                        @endif
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

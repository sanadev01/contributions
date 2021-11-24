@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                              Shipping Service : {{ $shipping_service->name }}
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.shipping-rates.index') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Return to List')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        Country
                                    </th>
                                    <th>
                                        Region
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shippingRegionRates as $rate)
                                <tr>
                                    <th>
                                        {{ optional($rate->country)->name }}
                                    </th>
                                    <th>
                                        {{ optional($rate->region)->name }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.view-shipping-region-rates', $rate) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-shipping-rates', $rate)}}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $shippingRegionRates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

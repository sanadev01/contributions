@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                All Zones
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.zone-profit.create') }}" class="pull-right btn btn-primary">
                                Upload Profit Rate
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        Zones
                                    </th>
                                    <th>
                                        Total Country
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zones as $zoneId => $services)
                                    @foreach($services as $serviceId => $zoneService)
                                        <tr>
                                            <th>
                                                Zone {{$zoneId}} | Service {{$zoneService->first()->shippingService->name}}
                                            </th>
                                            <th>
                                                {{ $zoneService->count() }}
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.rates.zone-profit-show', ['zone_id' => $zoneId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-primary btn-sm">
                                                    <i class="feather icon-eye"></i> View
                                                </a>
                                                |
                                                <a href="{{ route('admin.rates.downloadZoneProfit', ['zone_id' => $zoneId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-success btn-sm">
                                                    <i class="feather icon-download"></i> Download
                                                </a>
                                            </th>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

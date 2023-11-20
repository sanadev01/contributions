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
                                <tr>
                                    <th>
                                        Zone 1
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 1)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 1) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 1) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 2
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 2)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 2) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 2) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 3
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 3)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 3) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 3) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 4
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 4)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 4) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 4) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 5
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 5)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 5) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 5) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 6
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 6)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 6) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 6) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 7
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 7)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 7) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 7) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 8
                                    </th>
                                    <th>
                                        {{ $zones->where('zone_id', 8)->count() }}
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 8) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.downloadZoneProfit', 8) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

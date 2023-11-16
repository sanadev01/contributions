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
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 1) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 1) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 2
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 2) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 2) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 3
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 3) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 3) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 4
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 4) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 4) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 5
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 5) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 5) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 6
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 6) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 6) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 7
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 7) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 7) }}" class="btn btn-success btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Zone 8
                                    </th>
                                    <th>
                                        2
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.rates.zone-profit.show', 8) }}" class="btn btn-primary btn-sm">
                                           <i class="feather icon-eye"></i> View
                                        </a>
                                        |
                                        <a href="{{ route('admin.rates.download-accrual-rates', 8) }}" class="btn btn-success btn-sm">
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

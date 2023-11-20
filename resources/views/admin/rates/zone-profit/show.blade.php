@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                Profit Rates of Zone {{$id}}
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                        <div class="row col-md-6">
                            <div class="ml-auto">
                                <a href="{{ route('admin.rates.zone-profit.index') }}" class="pull-right btn btn-primary ml-2">
                                    @lang('shipping-rates.Return to List')
                                </a>
                                <a href="{{ route('admin.rates.downloadZoneProfit', $id) }}" class="pull-right btn btn-success">
                                    @lang('shipping-rates.Download')
                                </a>
                            </div>    
                        </div>
                            
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
                                        Profit
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zoneProfit as $rate)
                                    <tr>
                                        <th>
                                            {{ $rate->country->name }}
                                        </th>
                                        <th>
                                            {{ $rate->profit_percentage }}
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

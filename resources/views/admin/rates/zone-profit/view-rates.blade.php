@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @if($type=="cost")
                                    Cost Rates of {{$service->name}} for Zone {{$zoneId}} @if($poboxNumber) - {{$poboxNumber}} @endif
                                @else
                                    Selling Rates of {{$service->name}} for Zone {{$zoneId}} @if($poboxNumber) - {{$poboxNumber}} @endif
                                @endif
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                        <div class="row col-md-6">
                            <div class="ml-auto">
                                <a href="{{ route('admin.rates.zone-profit.index') }}" class="pull-right btn btn-primary ml-2">
                                    @lang('shipping-rates.Return to List')
                                </a>
                                {{-- <a href="{{ route('admin.rates.downloadZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="pull-right btn btn-success">
                                    @lang('shipping-rates.Download')
                                </a> --}}
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
                                        Weight in KG
                                    </th>
                                    <th>
                                        Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rate['data'] as $weight => $rate)
                                    <tr>
                                        <td>
                                            {{ $weight }}
                                        </td>
                                        <td>
                                            {{ number_format($rate, 2) }}
                                        </td>
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


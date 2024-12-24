@extends('layouts.master')

@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 mr-3">
                        All Zones
                    </h4>
                    <div>
                        @can('create', App\Models\Rate::class)
                        <a href="{{ route('admin.rates.zone-cost-upload') }}" class="btn btn-primary">
                            Upload Rates
                        </a>
                        <a href="{{ route('admin.rates.zone-profit.create') }}" class="btn btn-primary ml-2">
                            Upload Profit
                        </a>

                        @endcan


                    </div>
                </div>
                <hr>
                <div class="card-content card-body">
                    <table class="table table-bordered table-responsive-md">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('admin.rates.zone-profit.index', ['sort' => 'group_id', 'order' => request('sort') == 'group_id' && request('order') == 'desc' ? 'asc' : 'desc']) }}">
                                        Zone
                                        @if(request('sort') == 'group_id')
                                        @if(request('order') == 'asc')
                                        &#8593;
                                        @else
                                        &#8595;
                                        @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.rates.zone-profit.index', ['sort' => 'shipping_service_id', 'order' => request('sort') == 'shipping_service_id' && request('order') == 'desc' ? 'asc' : 'desc']) }}">
                                        Shipping Service
                                        @if(request('sort') == 'shipping_service_id')
                                        @if(request('order') == 'asc')
                                        &#8593;
                                        @else
                                        &#8595;
                                        @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Zipcode Start</th>
                                <th>Zipcode End</th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $groupId => $services)
                            @foreach($services as $serviceId => $groupService)
                            <tr>
                                <th>
                                    Zone {{$groupId}}
                                </th>
                                <th>
                                    {{$groupService->first()->shippingService->name}}
                                </th>
                                <th>
                                    {{ getGroupRange($groupId)['start'] }}
                                </th>
                                <th>
                                    {{ getGroupRange($groupId)['end'] }}
                                </th>
                                <th>
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.rates.zone-profit-show', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-primary btn-sm">
                                        <i class="feather icon-eye"></i> View
                                    </a>
                                    |
                                    <a href="{{ route('admin.rates.downloadZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-success btn-sm">
                                        <i class="feather icon-download"></i> Download
                                    </a>
                                    |
                                    <form action="{{ route('admin.rates.destroyZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-md">
                                            <i class="feather icon-trash px-1"></i>
                                        </button>
                                    </form>
                                    |
                                    @endif
                                    @if($rates->contains('shippingService.id', $serviceId))
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 120px; height:27px; padding-top:3px;">
                                            View Rates
                                        </button>
                                        <div class="dropdown-menu">
                                            @foreach($rates as $rate)
                                            @if($rate->shippingService->id == $serviceId)
                                            @if(isset($rate->cost_rates) && Auth::user()->isAdmin())
                                            @php
                                            $costRateLabel = ($rate->shippingService->is_pasarex ? 'Accrual Rate' : 'Cost Rate') . ' - ' . ($rate->user ? $rate->user->pobox_number : 'All');
                                            $decodedCostRates = json_decode($rate->cost_rates, true);
                                            $zoneExists = isset($decodedCostRates["Zone $groupId"]);
                                            @endphp
                                            @if($zoneExists)
                                            <a class="dropdown-item" href="{{ route('admin.rates.view-zone-cost', ['shipping_service_id' => $serviceId, 'zone_id' => $groupId, 'type' => 'cost', 'user_id' => $rate->user ? $rate->user->id : null]) }}">{{ $costRateLabel }}</a>
                                            @endif
                                            @endif

                                            @if(isset($rate->selling_rates) )
                                            @php
                                            $sellingRateLabel = $rate->user ? 'Selling Rate - ' . $rate->user->pobox_number : 'Selling Rate - All';
                                            $decodedSellingRates = json_decode($rate->selling_rates, true);
                                            $zoneExists = isset($decodedSellingRates["Zone $groupId"]);
                                            @endphp
                                            @if($zoneExists)
                                            <a class="dropdown-item" href="{{ route('admin.rates.view-zone-cost', ['shipping_service_id' => $serviceId, 'zone_id' => $groupId, 'type' => 'package', 'user_id' => $rate->user ? $rate->user->id : null]) }}">{{ $sellingRateLabel }}</a>
                                            @endif
                                            @endif
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    @endif
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
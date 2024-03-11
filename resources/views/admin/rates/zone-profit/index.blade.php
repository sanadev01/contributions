@extends('layouts.master')

@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="">
                        <h4 class="mb-0 mr-3">
                            All Groups
                        </h4>
                        <hr>
                    </div>
                    @can('create', App\Models\Rate::class)
                        <a href="{{ route('admin.rates.zone-cost-upload') }}" class="pull-right btn btn-primary">
                            Upload Rates
                        </a>
                        <a href="{{ route('admin.rates.zone-profit.create') }}" class="pull-right btn btn-primary">
                            Upload Profit
                        </a>
                    @endcan
                </div>
                <hr>
                <div class="card-content card-body">
                    <table class="table table-bordered table-responsive-md">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('admin.rates.zone-profit.index', ['sort' => 'group_id', 'order' => request('sort') == 'group_id' && request('order') == 'desc' ? 'asc' : 'desc']) }}">
                                        Group
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
                                    Group {{$groupId}}
                                </th>
                                <th>
                                    {{$groupService->first()->shippingService->name}}
                                </th>
                                <th>
                                    <a href="{{ route('admin.rates.zone-profit-show', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-primary btn-sm">
                                        <i class="feather icon-eye"></i> View
                                    </a>
                                    |
                                    <a href="{{ route('admin.rates.downloadZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="btn btn-success btn-sm">
                                        <i class="feather icon-download"></i> Download
                                    </a>
                                    | <form action="{{ route('admin.rates.destroyZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn  btn-danger btn-md">
                                            <i class="feather icon-trash px-1"></i>
                                        </button>
                                    </form>
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
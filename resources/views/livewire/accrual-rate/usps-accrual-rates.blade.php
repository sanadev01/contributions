<div>

    <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
        <div class=" col-7 text-left mb-2 pl-0">
            <div class="row col-12 my-3 pl-0" id="dateSearch">
                <form class="d-flex col-12 pl-0" action="http://hd-v2.test/affiliate/sale-exports" method="GET" target="_blank">
                    <input type="hidden" name="_token" value="Ms68RWi4bWZCVayoFufKqgHgWFd34bT6Vv2xwWLt">
                    <div class="form-group mb-2 col-3" style="float:left;margin-right:20px;">
                        <label>Start Date</label>
                        <input type="date" wire:model.defer="start_date" class="form-control" id="start_date">
                    </div>
                    <div class="form-group mb-2 col-3">
                        <label>End Date</label>
                        <input type="date" wire:model.defer="end_date" class="form-control" id="end_date">
                    </div>
                    <div class="form-group mb-2 col-3 mt-1" style="float:left;margin-right:20px;">
                        <button type="button" wire:click="download()" class="btn btn-success mt-4"
                            @if (!$searchOrders) disabled @endif><i
                                class="feather icon-download"></i></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    @if ($error)
        <div class="form-row">
            <div class="form-group col-md-12">
                <h4 class="text-danger mt-4">{{ $error }}</h4>
            </div>
        </div>
    @endif
    <div class="mt-3">
        <table class="table table-bordered">
            <tr>
                <th>@lang('shipping-rates.Customer ID')</th>
                <th>@lang('shipping-rates.Order')</th>
                <th>@lang('shipping-rates.Tracking Number')</th>
                <th>@lang('shipping-rates.Paid To USPS')</th>
                <th>@lang('shipping-rates.Charge To Customer')</th>
                <th>@lang('shipping-rates.Service')</th>
                <th>@lang('shipping-rates.Pieces')</th>
                <th>@lang('shipping-rates.Weight')</th>
                <th>@lang('shipping-rates.Zip Code Origin')</th>
                <th>@lang('shipping-rates.Zip Code Destination')</th>
            </tr>
            @if ($searchOrders)
                @foreach ($searchOrders as $key => $order)
                    <tr>
                        <td>
                            {{ $order[0]->user->pobox_number }}
                        </td>
                        <td>
                            @foreach ($order as $parcel)
                                {{ $parcel->warehouse_number }},
                            @endforeach
                        </td>
                        <td>
                            {{ $order[0]->us_api_tracking_code }}
                        </td>
                        <td>
                            {{ $order[0]->getUspsResponse()->total_amount }} USD
                        </td>
                        <td>
                            {{ $order[0]->usps_cost }} USD
                        </td>
                        <td>
                            {{ $order[0]->getUspsResponse()->usps->mail_class }}
                        </td>
                        <td>
                            {{ $order->count() }}
                        </td>
                        <td>
                            {{ $order[0]->getUspsResponse()->weight }}
                            {{ $order[0]->getUspsResponse()->weight_unit }}
                        </td>
                        <td>
                            {{ $order[0]->getUspsResponse()->from_address->postal_code }}
                        </td>
                        <td>
                            {{ $order[0]->getUspsResponse()->to_address->postal_code }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
</div>

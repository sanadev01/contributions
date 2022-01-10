<div>
    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="start_date">Start Date</label>
            <input type="date" wire:model.defer="start_date" class="form-control" id="start_date">
        </div>
        <div class="form-group col-md-2">
            <label for="end_date">End Date</label>
            <input type="date" wire:model.defer="end_date" class="form-control" id="end_date">
        </div>
        <div class="form-group col-md-2 mt-4 ml-3">
            <button type="button" wire:click="search" class="btn btn-primary">Search</button>
        </div>
        <div class="form-group col-md-2 mt-4 ml-3">
            <button type="button" wire:click="download()" class="btn btn-primary"  @if(!$searchOrders) disabled @endif>@lang('shipping-rates.Download')</button>
        </div>
    </div>
    @if ($error)
        <div class="form-row">
            <div class="form-group col-md-12">
                <h4 class="text-danger mt-4">{{ $error}}</h4>
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
            @if($searchOrders)
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
                        {{ $order->count()}}
                    </td>
                    <td>
                        {{ $order[0]->getUspsResponse()->weight }}  {{$order[0]->getUspsResponse()->weight_unit}}
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

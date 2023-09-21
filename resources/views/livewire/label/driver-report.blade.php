<div>
    <div class="col-12 row mb-5">
        <div class="col-12 d-flex justify-content-end">
            <button type="button" wire:click="download" class="btn btn-success mr-2" @if(!$orders) disabled @endif>
                <i class="feather icon-download"></i> @lang('orders.import-excel.Download') Report
            </button>
        </div>
        <div class="row col-12 d-flex justify-content-end">
            <form wire:submit.prevent="search" class="col-12">
                <div class="row col-12">
                    <div class="offset-7 col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">Start Date</label>
                                <input class="form-control" type="date" wire:model.defer="start_date" required>
                                @error("start_date")
                                <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">End Date</label>
                                <input class="form-control" type="date" wire:model.defer="end_date" required>
                                @error("end_date")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <div class="controls">
                                @if ($hasSearch)
                                    <button type="button" wire:click="clearSearch" class="btn btn-primary mt-4" wire:click="search">
                                        <i class="feather icon-search"></i>  clear search
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary mt-4" wire:click="search">
                                        <i class="feather icon-search"></i>  Search
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>@lang('orders.print-label.Barcode')</th>
            <th>PO Box#</th>
            <th>@lang('orders.print-label.Driver')</th>
            <th>@lang('orders.print-label.Client')</th>
            <th>@lang('orders.print-label.Dimensions')</th>
            <th>@lang('orders.print-label.Kg')</th>
            <th>@lang('orders.print-label.Reference')#</th>
            <th>@lang('orders.print-label.Recpient')</th>
            <th>@lang('orders.print-label.Date')</th>
            <th>@lang('orders.print-label.Pickup Date')</th>
        </tr>
        @if ($orders)
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->corrios_tracking_code }}</td>
                    <td>{{ $order->user->pobox_number }}</td>
                    <td>{{ optional(optional($order->driverTracking)->user)->name }}</td>
                    <td>{{ $order->merchant }}</td>
                    <td>{{ $order->length }} x {{ $order->length }} x {{ $order->height }}</td>
                    <td>{{ $order->getWeight('kg') }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->recipient->first_name }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ optional($order->driverTracking)->created_at }}</td>
                </tr>
            @endforeach
        @endif
    </table>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $orders->links() }}
    </div>
</div>

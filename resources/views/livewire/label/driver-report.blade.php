<div>
    <div class="hd-card mt-1 mb-3 pl-3" @if ($this->start_date || $this->end_date) style="display: block !important" @endif
        id="searchBlock">
        <div class="d-flex pl-1">
            <form wire:submit.prevent="search" class="col-12 p-0">
                <div class="row col-12 p-0 m-0">
                    <div class="col-2 pl-0">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">Start Date</label>
                                <input class="form-control hd-search" type="date" wire:model.defer="start_date"
                                    required>
                                @error('start_date')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">End Date</label>
                                <input class="form-control hd-search" type="date" wire:model.defer="end_date"
                                    required>
                                @error('end_date')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-2 pl-0">
                        <div class="form-group">
                            <div class="controls">
                                @if ($hasSearch)
                                    <button type="button" wire:click="clearSearch" class="btn btn-primary hd-mt-20"
                                        wire:click="search">
                                        <i class="feather icon-search"></i> clear search
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary hd-mt-20" wire:click="search">
                                        <i class="feather icon-search"></i>
                                    </button>
                                @endif
                                <button type="button" wire:click="download" class="hd-mt-20 btn btn-success"
                                    @if (!$orders) disabled @endif>
                                    <i class="feather icon-download"></i>
                                </button>
                                <button class="btn btn-primary ml-1 hd-mt-20 waves-effect waves-light"
                                    onclick="window.location.reload();">
                                    <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 d-flex justify-content-end p-0 pr-3">
                        <div class="form-group">
                            <div class="controls">

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
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
        </thead>
        <tbody>
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
        </tbody>
    </table>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $orders->links() }}
    </div>
</div>

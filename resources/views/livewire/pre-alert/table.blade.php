<div class="p-2">
    <div class="table-responsive order-table">
        <div class="row col-8 pr-0 pl-1 " id="singleSearch">
            <div class="form-group singleSearchStyle col-12">
                <label>Start Date</label>
                <input wire:model="custom" type="search" class="form-control col-8 hd-search" name="custom">
            </div>
        </div>
        <table class="table mb-0 table-bordered">
            <thead>
                <tr>
                    <th>
                        @lang('parcel.Date')
                        <a class="fas fa-sort text-right custom-sort-arrow" wire:click.prevent=sortBy('created_at')>
                        </a>
                    </th>
                    @admin
                        <th>@lang('parcel.User Name')</th>
                        <th>@lang('parcel.Pobox')#</th>
                    @endadmin
                    <th>@lang('parcel.wr')</th>
                    <th class="col-2">@lang('parcel.Gross Weight')</th>
                    <th class="col-2">@lang('parcel.Volume Weight')</th>
                    {{-- <th>@lang('parcel.Merchant')</th> --}}
                    {{-- <th>@lang('parcel.Carrier')</th> --}}
                    <th>@lang('parcel.Tracking ID')</th>
                    <th>@lang('parcel.Status')</th>
                    <th>@lang('parcel.Action')</th>
                </tr>
            </thead>
            <tbody>
                {{-- <tr>
                    <th><input class="form-control" type="text" wire:model="date"></th>
                    @admin
                        <th><input class="form-control" type="text" wire:model="name"></th>
                        <th><input class="form-control" type="text" wire:model="pobox"></th>
                    @endadmin
                    <th><input class="form-control" type="text" wire:model="whr_number"></th>
                    <th></th>
                    <th></th>
                    <th><input class="form-control" type="text" wire:model="merchant"></th>
                    <th><input class="form-control" type="text" wire:model="carrier"></th>
                    <th><input class="form-control" type="text" wire:model="tracking_id"></th>
                    <th>
                        <select class="form-control" wire:model="status">
                            <option value="">@lang('parcel.All')</option>
                            <option value="transit">@lang('parcel.Transit')</option>
                            <option value="ready">@lang('parcel.Ready')</option>
                            <option value="25">@lang('consolidation.Consolidation Requested')</option>
                            <option value="26">@lang('consolidation.Consolidated')</option>
                        </select>
                    </th>
                    <th></th>
                </tr> --}}
                @forelse($parcels as $parcel)
                    @include('admin.parcels.components.parcel-row', ['parcel' => $parcel])
                @empty
                    <x-tables.no-record colspan="9"></x-tables.no-record>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="row pt-4">
        <div class="col-1">
            <select class="form-control hd-search mb-2" wire:model="pageSize">
                <option value="1">1</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="300">300</option>
            </select>
        </div>

        <div class="d-flex justify-content-end my-2 pb-4">
            {{ $parcels->links() }}
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

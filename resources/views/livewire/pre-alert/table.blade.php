<div class="p-2">
    <div class="table-responsive order-table">
        <div class="row col-8 pr-0 pl-0 " id="singleSearch"
            @if ($this->search) style="display: block !important;" @endif>
            <div class="form-group singleSearchStyle col-12">
                <form wire:submit.prevent="render">
                    <label>Search</label>
                    <div class="d-flex">
                        <input wire:model.defer="search" type="search" class="form-control col-8 hd-search"
                            name="search">
                        <button type="submit" class="btn btn-primary mb-1 waves-effect waves-light ml-1"
                            wire:click="render"><i class="feather icon-search"></i></button>
                        <button class="btn btn-primary ml-1 mb-1 waves-effect waves-light"
                            onclick="window.location.reload();">
                            <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                aria-hidden="true"></i></button>
                    </div>
                </form>
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
                    <th>@lang('parcel.Merchant')</th>
                    <th>@lang('parcel.Carrier')</th>
                    <th>@lang('parcel.Tracking ID')</th>
                    <th>@lang('parcel.Status')</th>
                    <th>@lang('parcel.Action')</th>
                </tr>
            </thead>
            <tbody>
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

        <div class="col-11 d-flex justify-content-end my-2 pb-4">
            {{ $parcels->links() }}
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

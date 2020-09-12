<div class="p-2">
    <div class="row">
        <div class="col-1">
            <select class="form-control" wire:model="pageSize">
                <option value="1">1</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="300">300</option>
            </select>
        </div>
    </div>
    <table class="table mb-0 table-responsive-md" style="zoom:0.8">
        <thead>
            <tr>
                <th>
                    <a href="#" wire:click.prevent=sortBy('created_at')>
                        @lang('prealerts.date')
                    </a>
                </th>
                @admin
                <th>User Name</th>
                <th>Pobox#</th>
                @endadmin
                <th>@lang('prealerts.wr')</th>
                <th>@lang('prealerts.gross-weight')</th>
                <th>@lang('prealerts.volume-weight')</th>
                <th>@lang('prealerts.merchant')</th>
                <th>@lang('prealerts.carrier')</th>
                <th>@lang('prealerts.tracking-id')</th>
                <th>@lang('prealerts.status')</th>
                <th>@lang('prealerts.actions.text')</th>
            </tr>
        </thead>
        <tbody>
            <tr>
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
                        <option value="">All</option>
                        <option value="transit">Transit</option>
                        <option value="ready">Ready</option>
                    </select>
                </th>
                <th></th>
            </tr>
            @forelse($parcels as $parcel)
                @include('admin.parcels.components.parcel-row',['parcel'=>$parcel])
            @empty
                <x-tables.no-record colspan="9"></x-tables.no-record>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-end my-2 pb-4">
        {{ $parcels->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>

<div>
    <div class="table-actions">
        <div class="row mb-3">
            <div class="col-2">
                <select wire:model='pageSize' class="form-control d-flex w-auto">
                    <option value="10">10</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
            </div>
            <div class="offset-8 col-2">
                <form action="{{ route('admin.export.addresses') }}" method="GET" target="_blank">
                    <div class="col-md-12 text-right pr-0">
                        <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                            Export Addresses <i class="fa fa-arrow-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <table class="table table-responsive-md mb-0">
        <thead>
            <tr>
                <th>
                    @lang('address.User')
                </th>
                <th>
                    <a href="#" wire:click.prevent="sortBy('first_name')">
                        @lang('address.Name')
                        @if ( $sortBy == 'first_name' && $sortAsc )
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif( $sortBy =='first_name' && !$sortAsc )
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif
                    </a>
                </th>
                <th>@lang('address.Address') </th>
                <th>@lang('address.Address')2 </th>
                <th>@lang('address.Street No')</th>
                <th>@lang('address.Country') </th>
                <th>@lang('address.City') </th>
                <th>@lang('address.State') </th>
                <th>@lang('address.CPF') </th>
                <th>@lang('address.CNPJ') </th>
                <th>@lang('address.Telefone') </th>
                <th>@lang('address.Actions') </th>
            </tr>
            <tr>
                <th>
                    <input type="search" wire:model.debounce.500ms="user" class="form-control">
                </th>
                <th><input type="search" wire:model.debounce.500ms="name" class="form-control"></th>
                <th><input type="search" wire:model.debounce.500ms="address" class="form-control"></th>
                <th><input type="search" wire:model.debounce.500ms="address" class="form-control"></th>
                <th><input type="search" wire:model.debounce.500ms="streetNo" class="form-control"></th>
                <th></th>
                <th><input type="search" wire:model.debounce.500ms="city" class="form-control"></th>
                <th>
                    <select wire:model.debounce.500ms="state" class="form-control">
                        <option value="">All</option>
                        @foreach (states(30) as $state)
                            <option value="{{ $state->id }}">{{ $state->code }}</option>
                        @endforeach
                    </select>
                </th>
                <th></th>
                <th><input type="search" wire:model.debounce.500ms="phone" class="form-control"> </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($addresses as $address)
                @include('admin.addresses.address-row')
            @endforeach
        </tbody>
    </table>
    {{ $addresses->links() }}
    @include('layouts.livewire.loading')
</div>

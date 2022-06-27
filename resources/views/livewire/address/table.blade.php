<div>
    <div class="table-actions" style="display: flex; justify-content: space-between">
        <select wire:model='pageSize' class="form-control hd-search col-1 mb-2">
            <option value="10">10</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
        </select>
        {{-- <select id="visibilityToggle" class="form-control hd-search col-3 mb-2">
            <option selected value="">Column visibility</option>
            <option value="0">User</option>
            <option value="1">Name</option>
            <option value="2">Address</option>
            <option value="3">Address2</option>
            <option value="4">House Number</option>
            <option value="5">Country</option>
            <option value="6">City</option>
            <option value="7">State</option>
            <option value="8">CPF</option>
            <option value="9">CNPJ</option>
            <option value="10">Phone</option>
            <option value="11">Actions</option>
        </select> --}}
    </div>

    <table class="table mb-0  table-bordered">
        <thead>
            <tr id="th">
                <th id="">
                    @lang('address.User')
                </th>
                <th>
                    @lang('address.Name')
                    <a href="#" wire:click.prevent="sortBy('first_name')">
                        @if ($sortBy == 'first_name' && $sortAsc)
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif($sortBy == 'first_name' && !$sortAsc)
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif
                    </a>
                </th>
                <th class="hidden-lg">@lang('address.Address') </th>
                <th>@lang('address.Address')2 </th>
                <th>@lang('address.Street No')</th>
                <th>@lang('address.Country') </th>
                <th>@lang('address.City') </th>
                <th>@lang('address.State') </th>
                <th>@lang('address.CPF') </th>
                <th id="colCnjp">@lang('address.CNPJ') </th>
                <th id="colPhone">@lang('address.Telefone') </th>
                <th id="colActions">@lang('address.Actions') </th>
            </tr>
            <tr id="th">
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
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($addresses as $address)
                @include('admin.addresses.address-row')
            @endforeach
        </tbody>
    </table>
    {{ $addresses->links() }}
    @include('layouts.livewire.loading')
</div>
<script>
    function toggleVisibility(value) {
        // console.log(value);
        const div = document.getElementById(value);
        console.log(div);
        if (div.style.display != 'block') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    }
</script>

        <div>
            <div class="mb-2 row col-md-12 pl-0 hide"@if ($this->search) style="display: block !important;" @endif
                id="logSearch">
                @admin
                    <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                        <div class="col-6">
                            <label>Search</label>
                            <input type="search" wire:model.defer="search" class="form-control">
                        </div>
                        <div class="mt-1">
                            <button type="submit" class="btn btn-primary mt-4">
                                <i class="fa fa-search"></i>
                            </button>
                            <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                                onclick="window.location.reload();">
                                <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                    aria-hidden="true"></i></button>
                        </div>
                    </form>
                @endadmin
                {{-- <div class="col-2">
            <label>Address</label>
            <input type="search" wire:model.debounce.500ms="address" class="form-control">
        </div>
        <div class="col-2">
            <label>Address 2</label>
            <input type="search" wire:model.debounce.500ms="address" class="form-control">
        </div>
        <div class="col-2">
            <label>House Number</label>
            <input type="search" wire:model.debounce.500ms="streetNo" class="form-control">
        </div>
        <div class="col-1">
            <label>City</label>
            <input type="search" wire:model.debounce.500ms="city" class="form-control">
        </div>
        <div class="col-1">
            <label>State</label>
            <select wire:model.debounce.500ms="state" class="form-control">
                <option value="">All</option>
                @foreach (states(30) as $state)
                    <option value="{{ $state->id }}">{{ $state->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-1">
            <label>CNPJ</label>
            <input type="search" wire:model.debounce.500ms="phone" class="form-control">
        </div> --}}

            </div>
            <table class="table mb-0  table-bordered">
                <thead>
                    <tr id="th">
                        <th id="">
                            @lang('address.User')
                        </th>
                        <th>
                            @lang('address.Name')
                            <a wire:click.prevent="sortBy('first_name')"
                                class="fas fa-sort text-right custom-sort-arrow" aria-hidden="true"></a>
                            {{-- <a href="#" wire:click.prevent="sortBy('first_name')">
                        @if ($sortBy == 'first_name' && $sortAsc)
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif($sortBy == 'first_name' && !$sortAsc)
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif --}}
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
                    {{-- <tr id="th">
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
            </tr> --}}
                </thead>
                <tbody>
                    @foreach ($addresses as $address)
                        @include('admin.addresses.address-row')
                    @endforeach
                </tbody>
            </table>
            <div class="row d-flex justify-content-between">
                <div class="col-1 hd-mt-1 pt-5 pr-0">
                    <select class="form-control hd-search" wire:model="pageSize">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="300">300</option>
                    </select>
                </div>
                <div class=" col-10 d-flex justify-content-end pr-2 pt-5 mx-2">
                    {{ $addresses->links() }}
                </div>
            </div>
            @include('layouts.livewire.loading')
        </div>
        <script>
            function toggleVisibility(value) {
                // console.log(value);
                const div = document.getElementById(value);
                if (div.style.display != 'block') {
                    div.style.display = 'block';
                } else {
                    div.style.display = 'none';
                }
            }
        </script>

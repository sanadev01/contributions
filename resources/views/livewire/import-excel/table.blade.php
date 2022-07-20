        <div>
            <div class="p-2">
                <div class="mb-2 row col-md-12 pl-3 hide"
                    @if ($this->search || $this->date) style="display: flex !important;" @endif id="logSearch">
                    <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                        <div class="col-2 pl-0">
                            <label>Date</label>
                            <input type="search" class="form-control" wire:model.defer="date">
                        </div>
                        @admin
                            <div class="col-2">
                                <label>Search</label>
                                <input type="search" class="form-control" wire:model.defer="search">
                            </div>
                        @endadmin
                        <button type="submit" class="btn btn-primary ml-2 mt-4">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                    {{-- <div class="col-2">
                <label>File Name</label>
                <input type="search" class="form-control" wire:model.debounce.1000ms="file_name">
            </div>
            <div class="col-2">
                <label>Total</label>
                <input type="search" class="form-control" wire:model.debounce.1000ms="total">
            </div> --}}

                </div>
                <div class="table-wrapper position-relative">
                    <table class="table table-bordered" id="">
                        <thead>
                            <tr>
                                <th>Date</th>
                                @admin
                                    <th>@lang('orders.import-excel.User')</th>
                                @endadmin
                                <th>@lang('orders.import-excel.File Name')</th>
                                <th>Total</th>
                                <th class="width-100">@lang('orders.actions.actions')</th>
                            </tr>
                            {{-- <tr class="no-print">
                        <th>
                            <input type="search" class="form-control col-md-9" wire:model.debounce.1000ms="date">
                        </th>
                        @admin
                            <th>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                            </th>
                        @endadmin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="file_name">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="total">
                        </th>
                        <th></th>
                    </tr> --}}
                        </thead>
                        <tbody>
                            @forelse ($importOders as $order)
                                @include('admin.import-excel.components.order-row', ['order' => $order])
                            @empty
                                <x-tables.no-record colspan="5"></x-tables.no-record>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- <livewire:import-excel.edit.modal/> --}}
                </div>
                <div class="row mb-2 no-print mb-0 d-flex justify-content-between">
                    <div class="col-1 pt-5">
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
                    <div class="d-flex justify-content-end my-2 pb-4 mx-2 pt-5">
                        {{ $importOders->links() }}
                    </div>
                </div>
                @include('layouts.livewire.loading')
            </div>

        </div>

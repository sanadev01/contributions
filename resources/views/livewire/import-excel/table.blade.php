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

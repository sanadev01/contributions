<div>
    <div>
        <div class="p-2">
            <div class="row mb-2 no-print">
                <div class="row col-8 pr-0 pl-3" id="singleSearch" style="display: block;">
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
            </div>
            <div class="table-wrapper position-relative">
                <table class="table mb-0 table-bordered table-responsive-md table-striped" id="">
                    <thead>
                        <tr>
                            <th>Date</th>
                            @admin
                                <th>User Name</th>
                            @endadmin
                            <th>Loja/Cliente</th>
                            <th>Carrier Tracking</th>
                            <th>Referência do Cliente</th>
                            <th>Tracking Code</th>
                            <th>Errors</th>
                            <th>@lang('Action')</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse ($importedOrders as $order)
                            @include('admin.import-order.components.order-row', ['order' => $order])
                        @empty
                            <x-tables.no-record colspan="8"></x-tables.no-record>
                        @endforelse
                    </tbody>
                </table>

            </div>
            <div>
                <div class="col-1 pl-0 mt-5">
                    <select class="form-control mt-5" wire:model="pageSize">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="300">300</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end my-2 pb-4 mx-2">
                    {{ $importedOrders->links() }}
                </div>
            </div>
            @include('layouts.livewire.loading')
        </div>

    </div>

</div>

@section('modal')
    <x-modal />
@endsection

<div>
    <div>
        <div class="p-2">
            <div class="row mb-2 no-print">
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
            <div class="table-wrapper position-relative">
                <table class="table mb-0 table-responsive-md table-striped" id="">
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
                        <tr class="no-print">
                            <th>
                                <input type="search" class="form-control col-md-9" wire:model.debounce.1000ms="date">
                            </th>
                            @admin
                            <th>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                            </th>
                            @endadmin
                            <th>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="client">
                            </th>
                            <td>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="carrier">
                            </td>
                            <td>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="reference">
                            </td>
                            <th>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="tracking">
                            </th>
                            <th>
                                <select type="search" class="form-control" name="type" wire:model.debounce.1000ms="type">
                                    <option value="">@lang('orders.import-excel.Select Order')</option>
                                    <option value="good">@lang('orders.import-excel.Good')</option>
                                    <option value="error">@lang('orders.import-excel.Error')</option>
                                </select>
                            </th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($importedOrders as $order)
                            @include('admin.import-order.components.order-row',['order'=>$order])    
                        @empty
                            <x-tables.no-record colspan="8"></x-tables.no-record>
                        @endforelse
                    </tbody>
                </table>
                
            </div>
            <div class="d-flex justify-content-end my-2 pb-4 mx-2">
                {{ $importedOrders->links() }}
            </div>
            @include('layouts.livewire.loading')
        </div>
        
    </div>
    
</div>

@section('modal')
    <x-modal/>
@endsection

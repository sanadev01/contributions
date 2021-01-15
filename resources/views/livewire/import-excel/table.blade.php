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
            <table class="table mb-0 table-responsive-md" id="">
                <thead>
                    <tr>
                        <th>Date</th>
                        @admin
                            <th>User</th>
                        @endadmin
                        <th>File Name</th>
                        <th>Total</th>
                        <th>Action</th>
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
                            <input type="search" class="form-control" wire:model.debounce.1000ms="file_name">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="total">
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($importOders as $order)
                        @include('admin.import-excel.components.order-row',['order'=>$order])    
                    @empty
                        <x-tables.no-record colspan="5"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
            {{-- <livewire:import-excel.edit.modal/> --}}
        </div>
        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
            {{ $importOders->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
    
</div>

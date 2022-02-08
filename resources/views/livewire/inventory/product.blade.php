<div>
    <div class="p-2" >
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
            <div class="col-11 text-right">
                <a href="{{ route('admin.inventory.product-export.index') }}" class="btn btn-success" title="Download">
                    <i class="fa fa-arrow-down">Download</i>
                </a>
            </div>
        </div>
        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-responsive-md" id="order-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        @admin
                        <th>User</th>
                        @endadmin
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>SKU</th>
                        <th>Active</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    <tr class="no-print">
                        <th>
                            <input type="search" class="form-control col-md-9" wire:model.debounce.1000ms="date">
                        </th>
                        @admin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="user">
                        </th>
                        @endadmin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="price">
                        </th>
                        <th></th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="sku">
                        </th>
                        <th >
                            <select class="form-control" wire:model="status">
                                <option value="">All </option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                            </select>
                        </th>
                        <th>
                            {{-- <input type="search" class="form-control" wire:model.debounce.1000ms="description"> --}}
                        </th>
                        <th></th>
                       
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    @include('admin.inventory.product.components.product-row',['product'=>$product])    
                    @empty
                        <x-tables.no-record colspan="9"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
            
        </div>
        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
            {{ $products->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(productId,status){
                $.post('{{route("admin.inventory.status.update")}}',{
                    product_id: productId,
                    status : status
                })
                .then(function(response){
                    if ( response.success ){
                        @this.call('render')
                    }else{
                        toastr.error(response.message)
                    }
                }).catch(function(data){
                    toastr.error(response.message)
                })
            })
        });
    </script>
@endpush

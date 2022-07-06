<div>
    <div class="p-2">
        <div class="row text-left">
            <div class="ml-auto mr-3 mb-2">
                <h1>Inventory Value: <span class="text-primary">$ {{ $inventoryValue }}</span></h1>
            </div>
        </div>
        <div class="row pl-3 mb-2 no-print d-flex justify-content-end">
            <div id="printBtnDiv">
                <button title="Print Labels" id="createSaleOrder" type="btn"
                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="">Create Sale
                        Order</i></button>
            </div>
            <div class="col-11 text-right">
                @admin
                    <a href="{{ route('admin.inventory.product-export.index') }}" class="btn btn-success"
                        title="Download">
                        <i class="fa fa-arrow-down"></i>
                    </a>
                @endadmin
            </div>
        </div>
        <div class="row col-8 pr-0 pl-1 " id="singleSearch"
            @if ($this->search) style="display: block !important;" @endif>
            <div class="form-group singleSearchStyle col-12">
                <label>Search</label>
                <input wire:model="search" type="search" class="form-control col-8 hd-search" name="custom">
            </div>
        </div>
        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-responsive table-bordered table-hover" id="order-table">
                <thead>
                    <tr>
                        <th id="optionChkbx">
                            <div class="vs-checkbox-con vs-checkbox-primary" title="Select All">
                                <input type="checkbox" id="checkAll" name="orders[]" class="check-all" value="">
                                <span class="vs-checkbox vs-checkbox-sm">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                        </th>
                        <th>
                            <a href="#" wire:click="sortBy('created_at')">
                                Date
                            </a>
                            @if ($sortBy == 'created_at' && $sortAsc)
                                <i class="fa fa-arrow-down ml-2"></i>
                            @elseif($sortBy == 'created_at' && !$sortAsc)
                                <i class="fa fa-arrow-up ml-2"></i>
                            @endif
                        </th>
                        @admin
                            <th>User</th>
                        @endadmin
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>SKU</th>
                        <th>Barcode</th>
                        <th>Weight</th>
                        <th>Unit</th>
                        <th>Inventroy Value</th>
                        <th>Exp Date</th>
                        <th>Description</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                    {{-- <tr class="no-print">
                        <th style="min-width: 100px;">
                            <select name="" id="bulk-actions" class="form-control">
                                <option value="clear">Clear All</option>
                                <option value="checkAll">Select All</option>
                                <option value="create-sale-order">Create Sale Order</option>
                            </select>
                        </th>
                        <th>
                            <input type="date" class="form-control col-md-9" wire:model.debounce.1000ms="date">
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
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="quantity">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="sku">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="barcode">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="weight">
                        </th>
                        <th>
                            <select class="form-control" wire:model="unit">
                                <option value="">Select Unit</option>
                                <option value="kg/cm">kg/cm</option>
                                <option value="lbs/in">lbs/in</option>
                            </select>
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="weight">
                        </th>
                        <th>
                            <input type="date" class="form-control" wire:model.debounce.1000ms="expdate">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="description">
                        </th>
                        <th>
                            <select class="form-control" wire:model="status">
                                <option value="">All </option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                            </select>
                        </th>
                        <th></th>

                    </tr> --}}
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @include('admin.inventory.product.components.product-row', ['product' => $product])
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>

        </div>
        <div class="row pl-0 pt-5 d-flex justify-content-between">
            <div class="col-1 pl-0 pt-5">
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
            <div class="d-flex justify-content-end pt-5 mx-2">
                {{ $products->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            @this.on('updated-status', function(productId, status) {
                $.post('{{ route('admin.inventory.status.update') }}', {
                        product_id: productId,
                        status: status
                    })
                    .then(function(response) {
                        if (response.success) {
                            @this.call('render')
                        } else {
                            toastr.error(response.message)
                        }
                    }).catch(function(data) {
                        toastr.error(response.message)
                    })
            })
        });
    </script>
@endpush

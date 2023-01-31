<div class="p-2">
    <div class="row mb-2 no-print pl-0">

        <div class="mb-2 row col-md-12 pl-4 mb-1"
            @if ($this->search) style="display: flex !important;" @endif id="hiddenSearch">
            <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                <div class="col-6 pl-2">
                    <label>Search</label>
                    <input type="search" class="form-control" wire:model.defer="search">
                </div>
                <div class="mt-1">
                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                        onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered table-responsive-md pb-4">
        <thead>
            <tr>
                @admin
                    <th>User Name</th>
                @endadmin
                <th>
                    @lang('orders.date')<a wire:click.prevent="sortBy('created_at')"
                        class="fas fa-sort text-right custom-sort-arrow"></a>
                </th>
                <th>
                    @lang('orders.order-id')<a wire:click.prevent="sortBy('id')"
                        class="fas fa-sort text-right custom-sort-arrow"></a>
                </th>
                <th>Tracking Code</th>
                <th>@lang('orders.amount')</th>
                <th>@lang('orders.status')</th>
                <th>@lang('orders.payment-status')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @include('admin.orders.components.order-row', ['order' => $order])
            @empty
                <x-tables.no-record colspan="9"></x-tables.no-record>
            @endforelse
        </tbody>
    </table>
    {{-- <livewire:order.bulk-edit.modal /> --}}

    <div class="row mt-4">
        <div class="col-1 pt-2 mt-4">
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
        <div class="col-11 pr-0 d-flex justify-content-end pr-3 pt-2 mt-4">
            {{ $orders->links() }}
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

@push('lvjs-stack')
    <script>
        function toggleHiddenSearch() {
            const div = document.getElementById('hiddenSearch');
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';

            }
        }
        window.addEventListener('DOMContentLoaded', () => {

            @this.on('updated-status',function(orderId,status){
                @this.call('render')
                $.post('{{url("/order/update/status")}}',{
                    order_id: orderId,
                    status : status
                })
                .then(function(response){
                    if ( response.success ){
                        toastr.success(response.message)
                        @this.call('render')
                    }else{
                        toastr.error(response.message)
                        @this.call('render')
                    }
                }).catch(function(data){
                    toastr.error(response.message)
                })

            })
        });
    </script>
@endpush

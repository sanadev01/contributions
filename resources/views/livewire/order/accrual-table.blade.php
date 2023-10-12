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
    </div>
    <div class=" ">
        
    <table class="table  table-borderless p-0 table-responsive-md table-striped" id="kpi-report">
                    <thead>
                        <tr id="kpiHead"> 
                    <th> 
                        <a href="#" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                    </th>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('id')">@lang('orders.order-id')</a> <i>  </i>
                    </th>
                    @admin
                    <th>User Name</th>
                    @endadmin   
                    <th>Carrier</th>
                    <th>Tracking Code</th> 
                    
                    <th><a href="#" wire:click.prevent="sortBy('gross_total')">@lang('orders.amount')</a></th>
                    <th> Amount Ext Tax & Duty</th>
                    <th>@lang('orders.payment-status')</th>
                    <!-- <th class="no-print">@lang('orders.actions.actions')</th> -->
                </tr>
                
                <tfoot class="search-header">
                 <tr id="kpiHeadSearch"> 
                    <th>
                        
                        <input type="search" class="form-control" wire:model.debounce.1000ms="date">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="whr_number">
                    </th>
                    @admin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                    </th>
                    @endadmin
                    <th>
                        <select class="form-control" wire:model.debounce.1000ms="carrier">
                            <option value="">All</option>
                            <option value="Brazil">Correios Brazil</option>
                            <option value="USPS">USPS</option>
                            <option value="UPS">UPS</option>
                            <option value="FEDEX">FEDEX</option>
                            <option value="Chile">Correios Chile</option>
                            <option value="Global eParcel">Global eParcel</option>
                            <option value="Prime5">Prime5</option>
                            <option value="Post Plus">Post Plus</option>
                            <option value="Total Express">Total Express</option>
                            <option value="HD Express">HD Express</option>
                        </select>
                    </th> 
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="amount">
                    </th>    
                    <th></th> 
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
            </thead>
            <tbody>
                @forelse ($orders as $order) 
                        @include('admin.orders.components.accrual-row',['order'=>$order])    
                @empty
                    <x-tables.no-record colspan="12"></x-tables.no-record>
                @endforelse
            </tbody>
        </table>
        <livewire:order.bulk-edit.modal/>
    </div>
    <div class="d-flex justify-content-end my-2 py-4 mx-2">
        {{ $orders->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(orderId,status){
                @this.call('render')
                $.post('{{route("admin.order.update.status")}}',{
                    _token: "{{ csrf_token() }}",
                    order_id: orderId,
                    status : status,
                    user: '{{auth()->user()->name}}'
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

            // @this.on('edit-order',function(){
            //     $('#order-table').addClass('w-25');
            //     $('#order-table').removeClass('w-100');
            // })
        });
    </script>
@endpush
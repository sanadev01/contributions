<div>
    <div class="p-2">
        <div class="row text-left">
            <div class="ml-auto mr-3 mb-2">
                <h1>Total Value: <span class="text-primary">$ {{ $totalValue }}</span></h1>
            </div>
        </div>
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
                <form action="{{ route('admin.inventory.orders.export') }}" method="GET" target="_blank">
                    <input type="hidden" name="pick" value="0">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="from-control col-2">
    
                    <label>End Date</label>
                    <input type="date" name="end_date" class="from-control col-2">
    
                    <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                        @lang('orders.Download Orders') <i class="fa fa-arrow-down"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive order-table">
            <table class="table mb-0 table-responsive-md" id="order-table">
                <thead>
                    <tr>
                        <th>
                            <span class="mr-4"></span>
                            <a href="#" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('id')">Sale Order Number</a> <i>  </i>
                        </th>
                        @admin
                        <th>User Name</th>
                        @endadmin
                        <th>Weight</th>
                        <th>Unit</th>
                        <th>@lang('orders.status')</th>
                        <th class="no-print">@lang('orders.actions.actions')</th>
                    </tr>
                    <tr class="no-print">
                        <th>
                            <input type="search" class="form-control col-md-9 ml-5" wire:model.debounce.1000ms="date">
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
                        </th>
                        <th>
                        </th>
                        <th>
                            <select class="form-control" wire:model="status">
                                <option value="">All</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_PENDING }}">PENDING</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_IN_PROGRESS }}">IN_PROGRESS</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_CANCELLED }}">CANCELLED</option>
                                {{-- <option value="{{ App\Models\Order::STATUS_INVENTORY_REJECTED }}">REJECTED</option> --}}
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_FULFILLED }}">FULFILLED</option>
                            </select>
                        </th>
                       
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if ( $order->isArrivedAtWarehouse() )
                                <i class="fa fa-star text-success p-1"></i>
                             @endif
                            @if( $order->warehouse_number)
                                <span>
                                    <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$order) }}">
                                        WRH#: {{ $order->warehouse_number }}
                                    </a>
                                </span>
                            @endif
                        </td>
                        @admin
                        <td>{{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}</td>
                        @endadmin
                        <td>{{ $order->weight }}</td>
                        <td>{{ $order->measurement_unit }}</td>
                        <td>
                            <select style="min-width:150px;" class="form-control {{ !auth()->user()->isAdmin() ? 'btn disabled' : ''  }} {{ $order->getStatusClass() }}" @if (auth()->user()->isAdmin())  wire:change="$emit('updated-status',{{$order}},$event.target.value)" @else disabled="disabled"  @endif>
                                <option class="bg-info" value="{{ App\Models\Order::STATUS_INVENTORY_PENDING }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_PENDING ? 'selected': '' }}>Pending</option>
                                <option class="bg-warning text-dark" value="{{ App\Models\Order::STATUS_INVENTORY_IN_PROGRESS }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_IN_PROGRESS ? 'selected': '' }}>In Progress</option>
                                <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_CANCELLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_CANCELLED ? 'selected': '' }}>CANCELLED</option>
                                {{-- <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_REJECTED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_REJECTED ? 'selected': '' }}>REJECTED</option> --}}
                                <option class="bg-success" value="{{ App\Models\Order::STATUS_INVENTORY_FULFILLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_FULFILLED ? 'selected': '' }}>Fulfilled</option>
                            </select>
                        </td>
                        <td>
                            <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.inventory.order.products',$order) }}" class="btn btn-primary">
                                <i class="feather icon-list"></i> @lang('orders.actions.view-products')
                            </button>
                        </td>
                    </tr>
                    @empty
                        <x-tables.no-record colspan="12"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $orders->links() }}
    </div>
    {{-- Modal --}}
    <div class="modal fade" id="orderUpdateModal" tabindex="-1" role="dialog" aria-labelledby="orderUpdateModal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="orderUpdateModal">Update Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <form id="update_order_form">
                    <div class="modal-body">
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="weight">Weight</label>
                            <input type="number" step="0.001" name="weight" class="form-control" id="weight" placeholder="Weight" required>
                            <span class="text-danger" id="weightError"></span>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="measurement_unit">Measuring Unit</label>
                            <select id="measurement_unit" class="form-control" required>
                                <option value="kg/cm">kg/cm</option>
                                <option value="lbs/in">lbs/in</option>
                            </select>
                            <span class="text-danger" id="measurement_unitError"></span>
                          </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="length">Length</label>
                                <input type="number" step="0.001" name="length" class="form-control" id="length" placeholder="length" required>
                                <span class="text-danger" id="lengthError"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="width">Width</label>
                                <input type="number" step="0.001" name="width" class="form-control" id="width" placeholder="width" required>
                                <span class="text-danger" id="widthError"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="height">Height</label>
                                <input type="number" step="0.001" name="height" class="form-control" id="height" placeholder="height" required>
                                <span class="text-danger" id="heightError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(order,status){

                if (status == 5) {
                    $('#orderUpdateModal').modal('show');

                    $('#weight').val(order.weight);
                    $('#measurement_unit').val(order.measurement_unit);
                    $('#length').val(order.length);
                    $('#width').val(order.width);
                    $('#height').val(order.height);

                    $('#update_order_form').submit(function(e) {
                        e.preventDefault();
                        
                        var weight = $('#weight').val();
                        let measurement_unit = $('#measurement_unit').val();
                        var length = $('#length').val();
                        var width = $('#width').val();
                        var height = $('#height').val();
                        var order_id = order.id;

                        $.ajax({
                            url: "{{ route('api.inventory.order.update') }}",
                            type: "POST",
                            data: {
                                weight: weight,
                                measurement_unit: measurement_unit,
                                length: length,
                                width: width,
                                height: height,
                                order_id: order_id,
                                status: status,
                            },
                            success: function(response) {
                                
                                if (response.success == true) {
                                    $('#orderUpdateModal').modal('hide');
                                    @this.call('render')
                                    return;
                                }

                                if (response.success == false) {
                                    if (response.errors.weight) {
                                        $('#weightError').text(response.errors.weight[0]);
                                    }
                                    if (response.errors.measurement_unit) {
                                        $('#measurement_unitError').text(response.errors.measurement_unit[0]);
                                    }
                                    if (response.errors.length) {
                                        $('#lengthError').text(response.errors.length[0]);
                                    }
                                    if (response.errors.width) {
                                        $('#widthError').text(response.errors.width[0]);
                                    }
                                    if (response.errors.height) {
                                        $('#heightError').text(response.errors.height[0]);
                                    }
                                    if (response.message) {
                                        toastr.error(response.message);
                                    }
                                }
                            },
                            error: function(response) {
                                console.log(response);
                                toastr.error(response.message)
                            }
                        })
                    });
                }else {
                    @this.call('render')
                    $.post('{{route("admin.order.update.status")}}',{
                        _token: "{{ csrf_token() }}",
                        order_id: order.id,
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
                }
            })

        });
    </script>
@endpush
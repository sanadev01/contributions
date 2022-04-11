@extends('layouts.master')

@section('page') 
    <section id="app">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Pickup Products</h4>
                        <div class="col-11 text-right">
                            <form action="{{ route('admin.inventory.orders.export') }}" method="GET" target="_blank">
                                <input type="hidden" name="pick" value="1">
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
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sale Order</th>
                                                <th>User Name</th>
                                                <th>Pobox Number</th>
                                                <th>Status</th>
                                                <th>Products / SKU</th>
                                                <th>Weight</th>
                                                <th>Unit</th>
                                                <th>Tracking Code</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($orders as $order)
                                                <tr>
                                                    <td>{{ $order->warehouse_number }}</td>
                                                    <td>{{ $order->user->name }}</td>
                                                    <td>{{ $order->user->pobox_number  }}</td>
                                                    <td>
                                                        <select style="min-width:150px;" class="form-control btn disabled {{ $order->getStatusClass() }}" disabled="disabled">
                                                            <option class="bg-info" value="{{ App\Models\Order::STATUS_INVENTORY_PENDING }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_PENDING ? 'selected': '' }}>Pending</option>
                                                            <option class="bg-warning text-dark" value="{{ App\Models\Order::STATUS_INVENTORY_IN_PROGRESS }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_IN_PROGRESS ? 'selected': '' }}>In Progress</option>
                                                            <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_CANCELLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_CANCELLED ? 'selected': '' }}>CANCELLED</option>
                                                            {{-- <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_REJECTED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_REJECTED ? 'selected': '' }}>REJECTED</option> --}}
                                                            <option class="bg-success text-dark" value="{{ App\Models\Order::STATUS_INVENTORY_FULFILLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_FULFILLED ? 'selected': '' }}>Fulfilled</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($order->products as $product)
                                                                <li>{{ $product->name }} / <b>{{ $product->sku }}</b></li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $order->weight }}</td>
                                                    <td>{{ $order->measurement_unit }}</td>
                                                    <td>{{ $order->corrios_tracking_code }}</td>
                                                    <td>
                                                        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.inventory.order.products',$order) }}" class="btn btn-primary">
                                                            <i class="feather icon-list"></i> @lang('orders.actions.view-products')
                                                        </button>
                                                        |
                                                        <a href="{{ route('admin.parcels.edit', $order) }}" class="btn btn-success">Place Order</a>
                                                    </td>
                                                </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            @endforelse   
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <x-modal/>
@endsection

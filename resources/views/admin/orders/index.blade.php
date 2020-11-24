@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">@lang('orders.orders')</h4>
        @can('canImportLeveOrders', App\Models\Order::class)
            <a href="{{ route('admin.leve-order-import.index') }}" class="pull-right btn btn-primary"> Import Leve Orders </a>
        @endcan
    </div>
    
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:order.table/>
        </div>
    </div>
</div>
@endsection


@section('modal')
    <x-modal/>
@endsection
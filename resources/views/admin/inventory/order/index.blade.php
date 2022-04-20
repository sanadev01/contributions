@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">Sales Orders</h4>
        <div class="col-1">
            {{-- <a href="{{ route('admin.inventory.product-order.create') }}" class="btn btn-primary">Create Sale Order</a> --}}
        </div>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:inventory.orders/>
        </div>
    </div>
    
</div>
@endsection

@section('modal')
    <x-modal/>
@endsection
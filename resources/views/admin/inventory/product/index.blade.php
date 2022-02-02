@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">Inventory Products</h4>
        
        <div>
            <a href="{{ route('admin.inventory.product.create') }}" class="btn btn-primary"> Add Product </a>
            <a href="{{ route('admin.inventory.product-import.create') }}" class="btn btn-info"> Import Products </a>
        </div>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:inventory.product :status="$status"/>
        </div>
    </div>
   
</div>
@endsection

@section('modal')
    <x-modal/>
@endsection


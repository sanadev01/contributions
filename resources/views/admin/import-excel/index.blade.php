@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">@lang('orders.orders')</h4>
            <a href="{{ route('admin.import.import-excel.create') }}" class="pull-right btn btn-primary"> Import Orders </a>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:import-excel.table/>
        </div>
    </div>
   
</div>
@endsection


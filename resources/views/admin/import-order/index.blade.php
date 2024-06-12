@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">@lang('orders.orders')</h4>
        <div>
            <a href="{{ route('admin.import.import-excel.index') }}" class="pull-right btn btn-primary"> @lang('orders.import-excel.Return to Back') </a>
            @if (request('type') == 'good')
                <a href="{{ route('admin.import.import-order.show',$orders) }}" class="pull-right btn btn-success mr-1"> @lang('orders.import-excel.Move All') </a>
            @endif
                
        </div>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:import-excel.imported-order :orders="$orders"/>
        </div>
    </div>
   
</div>
@endsection




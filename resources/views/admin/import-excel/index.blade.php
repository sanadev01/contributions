@extends('layouts.master')

@section('page')
    <div class="card min-vh-100">
        <div class="card-header">
        @section('title', __('orders.import-excel.Orders-uploaded'))
        <div class="col-12 d-flex justify-content-end pr-1">
            <div class="p-0 pr-1">
                <button onclick="toggleLogsSearch()" class="btn btn-primary waves-effect waves-light">
                    <i class="feather icon-search"></i>
                </button>
            </div>
            <a href="{{ route('admin.import.import-excel.create') }}" class="pull-right btn btn-primary">
                @lang('orders.import-excel.Import Orders')
            </a>
        </div>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:import-excel.table />
        </div>
    </div>

</div>
@endsection

@extends('layouts.master')
@section('css')
    <style>
       .btn-cancelled {
            color: #fff!important;
            background-color: #5a0000!important;
            border-color: #5a0000!important;
        }
       .btn-refund {
            color: #fff!important;
            background-color:red!important;
            border-color: red!important;
        }
    </style>
@endsection
@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">@lang('orders.Trashed Orders')</h4>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:order.trash/>
        </div>
    </div>
</div>
@endsection
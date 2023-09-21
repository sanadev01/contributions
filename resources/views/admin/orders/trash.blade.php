@extends('layouts.master')
@section('css')
    <style>
        .btn-cancelled {
            color: #fff !important;
            background-color: #5a0000 !important;
            border-color: #5a0000 !important;
        }

        .btn-refund {
            color: #fff !important;
            background-color: red !important;
            border-color: red !important;
        }
    </style>
@endsection
@section('page')
    <div class="card min-vh-100">
        <div class="offset-11 col-1 d-flex justify-content-end mt-3">
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                class="btn btn-primary mr-1"><i class="feather icon-filter"></i>
            </button>
            <button type="btn" onclick="toggleOrdersPageSearch()" id="ordersSearch"
                class="btn btn-primary mr-1"><i class="feather icon-search"></i>
            </button>
        </div>
    @section('title', __('orders.Trashed Orders'))
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">

            <livewire:order.trash />
        </div>
    </div>
</div>
@endsection

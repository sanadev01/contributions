@extends('layouts.master')

@section('page')
    <div class="card min-vh-100">
        <div class="card-header">
        @section('title', __('Inventory Products'))

        @can('create', App\Product::class)
            <div class="ml-2">
                <div id="printBtnDiv"class="d-block">
                    <button title="Print Labels" disabled id="createSaleOrder" type="btn"
                        class="btn btn-primary mr-1 mb-1 btn-disabled">Create Sale
                    </button>
                </div>
            </div>
            <div class="pr-0">
                @admin
                    <a href="{{ route('admin.inventory.product-export.index') }}" class="btn btn-success mr-1" title="Download">
                        <i class="fa fa-arrow-down"></i>
                    </a>
                @endadmin
                <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch" class="btn btn-primary mr-1">
                    <i class="feather icon-search"></i>
                </button>
                <a href="{{ route('admin.inventory.product.create') }}" class="btn btn-primary mr-1"> Add Product
                </a>
                <a href="{{ route('admin.inventory.product-import.create') }}" class="btn btn-info"> Import
                    Products
                </a>
            </div>
        @endcan
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:inventory.product />
        </div>
    </div>
    <form action="{{ route('admin.inventory.product-order.create') }}" method="GET" id="bulk_actions_form">
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
</div>
@endsection

@section('modal')
<x-modal />
@endsection

@section('js')
<script>
    $('body').on('change', '#checkAll', function() {

        if ($('#checkAll').is(':checked')) {
            $('.bulk-orders').prop('checked', true)
           $(".btn-disabled").removeAttr('disabled');
        } else {
            $('.bulk-orders').prop('checked', false)
           $(".btn-disabled").prop("disabled", true);
        }

    })
    $('body').on('click', '#createSaleOrder', function() {
        var productIds = [];
        $.each($(".bulk-orders:checked"), function() {
            productIds.push($(this).val());
        });

        $('#bulk_actions_form #command').val('create-sale-order');
        $('#bulk_actions_form #data').val(JSON.stringify(productIds));
        $('#bulk_actions_form').submit();

    })
</script>
@endsection

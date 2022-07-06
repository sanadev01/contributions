@extends('layouts.master')

@section('page')
    <div class="card min-vh-100">
        <div class="card-header">
        @section('title', __('Inventory Products'))
        {{-- <h4 class="mb-0">Inventory Products</h4> --}}

        <div class="col-12 pr-0">
            @can('create', App\Product::class)
                <div class="col-12 d-flex justify-content-end pr-0">
                    <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch"
                        class="btn btn-primary mr-1 waves-effect waves-light"><i class="feather icon-search"></i></button>
                    <a href="{{ route('admin.inventory.product.create') }}" class="btn btn-primary mr-1"> Add Product </a>
                    <a href="{{ route('admin.inventory.product-import.create') }}" class="btn btn-info"> Import Products
                    </a>
                </div>
            @endcan
        </div>
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
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            $('.bulk-orders').prop('checked', false)
            document.getElementById("printBtnDiv").style.display = 'none';
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

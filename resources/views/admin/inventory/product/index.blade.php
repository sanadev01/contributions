@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">Inventory Products</h4>
        
        <div>
            @can('create', App\Product::class)
                <a href="{{ route('admin.inventory.product.create') }}" class="btn btn-primary"> Add Product </a>
                <a href="{{ route('admin.inventory.product-import.create') }}" class="btn btn-info"> Import Products </a>
            @endcan
        </div>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:inventory.product/>
        </div>
    </div>
    <form action="{{ route('admin.inventory.product-order.create') }}" method="GET" id="bulk_actions_form">
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
</div>
@endsection

@section('modal')
    <x-modal/>
@endsection

@section('js')
    <script>
        $('body').on('change','#bulk-actions',function(){
            if ( $(this).val() == 'clear' ){
                $('.bulk-orders').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-orders').prop('checked',true)
            }else if ( $(this).val() == 'create-sale-order' ){
                var productIds = [];
                $.each($(".bulk-orders:checked"), function(){
                    productIds.push($(this).val());
                });
                
                $('#bulk_actions_form #command').val('create-sale-order');
                $('#bulk_actions_form #data').val(JSON.stringify(productIds));
                $('#bulk_actions_form').submit();
            }
        })
    </script>
@endsection
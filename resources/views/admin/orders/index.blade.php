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
        <h4 class="mb-0">@lang('orders.orders')</h4>
        @can('canImportLeveOrders', App\Models\Order::class)
            <a href="{{ route('admin.leve-order-import.index') }}" class="pull-right btn btn-primary"> Import Leve Orders </a>
        @endcan
    </div>
    
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:order.table :userType="$userType"/>
        </div>
    </div>
    <form action="{{ route('admin.order.bulk-action') }}" method="GET" id="bulk_actions_form">
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
            }else if ( $(this).val() == 'print-label' ){
                var orderIds = [];
                $.each($(".bulk-orders:checked"), function(){
                    orderIds.push($(this).val());
                });

                $('#bulk_actions_form #command').val('print-label');
                $('#bulk_actions_form #data').val(JSON.stringify(orderIds));
                $('#bulk_actions_form').submit();
            }
        })
    </script>
@endsection
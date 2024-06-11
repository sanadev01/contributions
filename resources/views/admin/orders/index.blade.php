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
    <form action="{{ route('admin.order.consolidate-domestic-label') }}" method="GET" id="consolidate_domestic_label_actions_form">
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
    <form action="{{ route('admin.trash-orders.destroy',1) }}" method="POST" id="trash_order_actions_form" onsubmit="return confirm('Are you Sure want to move trash?');">
        @csrf
        @method('DELETE')
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
</div>
<!--SEND MAIL MODAL-->
<div class="modal fade" id="mailModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-head">
                <h5 class="modal-title text-white"><b>@lang('orders.Enter message')</b></h5>
                <button type="button" class="close mt-0 mr-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
            </div>
            <form action="{{ route('admin.order.pre-alert') }}" method="GET" id="mail_form">
                <input type="hidden" name="command" id="command" value="">
                <input type="hidden" name="data" id="data" value="">
                <div class="modal-body">
                    <textarea class="form-control no-resize" name="message" rows="5"  placeholder="@lang('orders.Enter domestic TN')"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Pre-Alert</button>
                </div>
            </form>
        </div>
    </div>
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
            }else if ($(this).val() == 'consolidate-domestic-label'){
                var orderIds = [];
                $.each($(".bulk-orders:checked"), function(){
                    orderIds.push($(this).val());
                });

                $('#consolidate_domestic_label_actions_form #command').val('consolidate-domestic-label');
                $('#consolidate_domestic_label_actions_form #data').val(JSON.stringify(orderIds));
                $('#consolidate_domestic_label_actions_form').submit();
            }else if ($(this).val() == 'pre-alert'){
                var orderIds = [];
                $.each($(".bulk-orders:checked"), function() {
                    orderIds.push($(this).val());
                    console.log($(this).val());
                });
                $('#mailModal').modal('show');
                $('#mail_form #command').val('mail');
                $('#mail_form #data').val(JSON.stringify(orderIds));
            }else if ($(this).val() == 'move-order-trash'){
                var orderIds = [];
                $.each($(".bulk-orders:checked"), function(){
                    orderIds.push($(this).val());
                });

                $('#trash_order_actions_form #command').val('move-order-trash');
                $('#trash_order_actions_form #data').val(JSON.stringify(orderIds));
                $('#trash_order_actions_form').submit();
            }
        })
    </script>
@endsection
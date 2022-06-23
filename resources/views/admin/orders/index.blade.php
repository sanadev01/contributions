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
    <div class="col-8" style="display: flex;">
        <h4 class="mb-0 pt-1">@lang('orders.orders')</h4>
        <div id="printBtnDiv">
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch1" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-check-square"></i></button>
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch2" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-printer"></i></button>
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch3" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-printer"></i></button>
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch4" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-trash"></i></button>
        </div>
    </div>
       
        <div class="row filter" style="padding-right:1%;">
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch8" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-filter"></i></button>
            @can('canImportLeveOrders', App\Models\Order::class)
            <a href="{{ route('admin.leve-order-import.index') }}" class="pull-right btn btn-primary" style="height: max-content"> Import Leve Orders </a>
            @endcan
        </div>
    </div>
    
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:order.table :userType="$userType"/>
            <div class="col-1 pl-10 pb-10">
                <select class="form-control hd-search" style="padding-left: initial" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
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
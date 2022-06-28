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
@section('title', __('orders.orders'))

<div class="card min-vh-100">
    <div class="card-header">
        <div class="col-8 btnsDiv" style="display: flex;">
            <div id="printBtnDiv">
                <button title="Print Labels" id="print" type="btn"
                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                        class="feather icon-printer"></i></button>

                <button title="Print Domestic Labels" id="deomesticPrint" type="btn"
                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-tag"></i></button>
                <button title="Delete" id="trash" type="btn"
                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                        class="feather icon-trash"></i></button>
            </div>
        </div>

        <div class="row filter" style="padding-right:1%;">
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-filter"></i></button>
            @can('canImportLeveOrders', App\Models\Order::class)
                <a href="{{ route('admin.leve-order-import.index') }}" class="pull-right btn btn-primary"
                    style="height: max-content"> Import Leve Orders </a>
            @endcan
        </div>
    </div>

    <div class="card-content">
        <div class="card-body no-print pt-0" style="overflow-y: visible">
            <livewire:order.table :userType="$userType" />

        </div>
    </div>
    <form action="{{ route('admin.order.bulk-action') }}" method="GET" id="bulk_actions_form">
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
    <form action="{{ route('admin.order.consolidate-domestic-label') }}" method="GET"
        id="consolidate_domestic_label_actions_form">
        <input type="hidden" name="command" id="command" value="">
        <input type="hidden" name="data" id="data" value="">
    </form>
    <form action="{{ route('admin.trash-orders.destroy', 1) }}" method="POST" id="trash_order_actions_form"
        onsubmit="return confirm('Are you Sure want to move trash?');">
        @csrf
        @method('DELETE')
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
    // $(function () {
    //     $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' })
    // })
    
    // function copyCarrier(element)
    // {
    //   var copyText = document.getElementById("tracking");
    //   copyText.select();
    //   copyText.setSelectionRange(0, 99999)
    //   document.execCommand("copy");

    //     // var range = document.createRange();
    //     //         range.selectNode(document.getElementById("tracking"));
    //     //         window.getSelection().removeAllRanges(); // clear current selection
    //     //         window.getSelection().addRange(range); // to select text
    //     //         document.execCommand("copy");
    //     //         window.getSelection().removeAllRanges();// to deselect
    // }
    // $('body').on('change','#bulk-actions',function(){
    //     if ( $(this).val() == 'clear' ){
    //         $('.bulk-orders').prop('checked',false)
    //     }else if ( $(this).val() == 'checkAll' ){
    //         $('.bulk-orders').prop('checked',true)
    //     }else if ( $(this).val() == 'print-label' ){
    //         var orderIds = [];
    //         $.each($(".bulk-orders:checked"), function(){
    //             orderIds.push($(this).val());
    //         });

    //         $('#bulk_actions_form #command').val('print-label');
    //         $('#bulk_actions_form #data').val(JSON.stringify(orderIds));
    //         $('#bulk_actions_form').submit();
    //     }else if ($(this).val() == 'consolidate-domestic-label'){
    //         var orderIds = [];
    //         $.each($(".bulk-orders:checked"), function(){
    //             orderIds.push($(this).val());
    //         });

    //         $('#consolidate_domestic_label_actions_form #command').val('consolidate-domestic-label');
    //         $('#consolidate_domestic_label_actions_form #data').val(JSON.stringify(orderIds));
    //         $('#consolidate_domestic_label_actions_form').submit();
    //     }else if ($(this).val() == 'move-order-trash'){
    //         var orderIds = [];
    //         $.each($(".bulk-orders:checked"), function(){
    //             orderIds.push($(this).val());
    //         });

    //         $('#trash_order_actions_form #command').val('move-order-trash');
    //         $('#trash_order_actions_form #data').val(JSON.stringify(orderIds));
    //         $('#trash_order_actions_form').submit();
    //     }
    // })
    $('body').on('click', '#print', function() {
        var orderIds = [];
        $.each($(".bulk-orders:checked"), function() {
            orderIds.push($(this).val());
            console.log($(this).val());
        });
        console.log(JSON.stringify(orderIds));
        $('#bulk_actions_form #command').val('print-label');
        $('#bulk_actions_form #data').val(JSON.stringify(orderIds));
        $('#bulk_actions_form').submit();
    })
    $('body').on('click', '#domesticPrint', function() {
        var orderIds = [];
        $.each($(".bulk-orders:checked"), function() {
            orderIds.push($(this).val());
            console.log($(this).val());
        });
        $('#consolidate_domestic_label_actions_form #command').val('consolidate-domestic-label');
        $('#consolidate_domestic_label_actions_form #data').val(JSON.stringify(orderIds));
        $('#consolidate_domestic_label_actions_form').submit();
    })
    $('body').on('click', '#trash', function() {
        var orderIds = [];
        $.each($(".bulk-orders:checked"), function() {
            orderIds.push($(this).val());
            console.log($(this).val());
        });

        $('#trash_order_actions_form #command').val('move-order-trash');
        $('#trash_order_actions_form #data').val(JSON.stringify(orderIds));
        $('#trash_order_actions_form').submit();

    })
    $('body').on('change', '#checkAll', function() {

        if ($('#checkAll').is(':checked')) {
            $('.bulk-orders').prop('checked', true)
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            $('.bulk-orders').prop('checked', false)
            console.log($(".bulk-orders:checked").length);
            document.getElementById("printBtnDiv").style.display = 'none';
        }
        // console.log(flag);
        // if ( $(this).val() == 'clear' ){
        //     $('.bulk-orders').prop('checked',false)
        // }else if ( $(this).val() == 'checkAll' ){
        //     $('.bulk-orders').prop('checked',true)
        // }else if ( $(this).val() == 'print-label' ){
        //     var orderIds = [];
        //     $.each($(".bulk-orders:checked"), function(){
        //         orderIds.push($(this).val());
        //     });

        //     $('#bulk_actions_form #command').val('print-label');
        //     $('#bulk_actions_form #data').val(JSON.stringify(orderIds));
        //     $('#bulk_actions_form').submit();
        // }else if ($(this).val() == 'consolidate-domestic-label'){
        //     var orderIds = [];
        //     $.each($(".bulk-orders:checked"), function(){
        //         orderIds.push($(this).val());
        //     });

        //     $('#consolidate_domestic_label_actions_form #command').val('consolidate-domestic-label');
        //     $('#consolidate_domestic_label_actions_form #data').val(JSON.stringify(orderIds));
        //     $('#consolidate_domestic_label_actions_form').submit();
        // }else if ($(this).val() == 'move-order-trash'){
        //     var orderIds = [];
        //     $.each($(".bulk-orders:checked"), function(){
        //         orderIds.push($(this).val());
        //     });

        //     $('#trash_order_actions_form #command').val('move-order-trash');
        //     $('#trash_order_actions_form #data').val(JSON.stringify(orderIds));
        //     $('#trash_order_actions_form').submit();
        // }
    })
</script>
@endsection

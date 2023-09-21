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
    <div class="card-header pt-1">
        <div class="col-8 btnsDiv" style="display: flex;">
            <div id="printBtnDiv" style="display: block;">
                <small>@lang('orders.Click on')  <mark> @lang('orders.Checkbox') </mark> @lang('orders.to use these features') </small>
                <br>

                <button title="Print Labels" id="print" type="btn" disabled class="btn btn-primary mr-1 mb-1 btn-disabled">
                    <i class="feather icon-printer"></i>
                </button>
                <button title="Print Domestic Labels" id="domesticPrint" type="btn" disabled class="btn btn-primary mr-1 mb-1 btn-disabled">
                    <i class="feather icon-tag"></i>
                </button>
                <button title="Send Email Pre Alert" id="sendMail" type="btn" disabled class="btn btn-primary mr-1 mb-1 btn-disabled">
                    <i class="feather icon-mail"></i>
                </button>
                <button title="Delete multiple Orders" id="trash" type="btn" disabled class="btn btn-primary mr-1 mb-1 btn-disabled">
                    <i class="feather icon-trash"></i>
                </button>
            </div>
        </div>

        <div class="row filter pr-3">
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                class="btn btn-primary mr-1 mb-1"><i class="feather icon-filter"></i>
            </button>
            <button type="btn" onclick="toggleOrdersPageSearch()" id="ordersSearch"
                class="btn btn-primary mr-1 mb-1"><i class="feather icon-search"></i>
            </button>
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
    </form>
    <form action="{{ route('admin.trash-orders.destroy', 1) }}" method="POST" id="trash_order_actions_form"
        onsubmit="return confirm('Are you Sure want to move trash?');">
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
                    <textarea class="form-control no-resize" name="message" rows="5" placeholder="@lang('orders.Enter domestic TN')"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Pre-Alert</button>
                </div>
            </form>
        </div>
    </div>
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
<x-modal />
@endsection

@section('js')
<script>
    $('body').on('click', '#print', function() {
        var orderIds = [];
        $.each($(".bulk-orders:checked"), function() {
            orderIds.push($(this).val());
            console.log($(this).val());
        });
        console.log(JSON.stringify(orderIds));
        if(!jQuery.isEmptyObject(orderIds)){
            $('#bulk_actions_form #command').val('print-label');
            $('#bulk_actions_form #data').val(JSON.stringify(orderIds));
            $('#bulk_actions_form').submit();
        }
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
    $('body').on('click', '#sendMail', function() {
        var orderIds = [];
        $.each($(".bulk-orders:checked"), function() {
            orderIds.push($(this).val());
            console.log($(this).val());
        });
        $('#mailModal').modal('show');
        $('#mail_form #command').val('mail');
        $('#mail_form #data').val(JSON.stringify(orderIds));
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
            $(".btn-disabled").removeAttr('disabled');
        } else {
            $('.bulk-orders').prop('checked', false)
            console.log($(".bulk-orders:checked").length);
            $(".btn-disabled").prop("disabled", true);
        }

    })
</script>
@endsection

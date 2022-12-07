@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header"> --}}
                    @if (auth()->user()->isAdmin())
                        @section('title', __('sales-commission.Sales Commissions'))
                    @else
                    @section('title', __('sales-commission.My Sales Commissions'))
                @endif
                {{-- </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive-md">
                            <livewire:affiliate.table />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="confirm" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-8">
                            <h4>
                                Confirm to Pay Commission
                            </h4>
                        </div>
                    </div>
                    <form action="{{ route('admin.affiliate.sales-commission.create') }}" method="GET"
                        id="bulk_sale_form">
                        <div class="modal-body" style="font-size: 15px;">
                            <p>
                                Are you Sure want to Pay the Commissions against these selected orders
                                {{-- <span class="result"></span> --}}
                            </p>
                            <input type="hidden" name="command" id="command" value="">
                            <input type="hidden" name="data" id="data" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save"> Yes Pay</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                @lang('consolidation.Cancel')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@section('modal')
<x-modal />
@endsection


@section('js')
<script>
    $('body').on('change', '#checkAll', function() {

        if ($('#checkAll').is(':checked')) {
            $('.bulk-sales').prop('checked', true)
            $(".btn-disabled").removeAttr('disabled');
            // document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            $('.bulk-sales').prop('checked', false)
            $(".btn-disabled").prop("disabled", true);
            // document.getElementById("printBtnDiv").style.display = 'none';
        }

    })
    $('body').on('click', '#pay-commission', function() {
        var orderIds = [];
        $.each($(".bulk-sales:checked"), function() {
            orderIds.push($(this).val());

        });
        $('#bulk_sale_form #command').val('pay-commission');
        $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
        $('#confirm').modal('show');
    })
    $('body').on('change', '#bulk-actions', function() {
        if ($(this).val() == 'clear') {
            $('.bulk-sales').prop('checked', false)
        } else if ($(this).val() == 'checkAll') {
            $('.bulk-sales').prop('checked', true)
        } else if ($(this).val() == 'pay-commission') {
            var orderIds = [];
            $.each($(".bulk-sales:checked"), function() {
                orderIds.push($(this).val());

                // $(".result").append('HD-' + this.value + ',');
            });

            $('#bulk_sale_form #command').val('pay-commission');
            $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
            $('#confirm').modal('show');
            // $('#bulk_sale_form').submit();
        }
    })
</script>
@endsection

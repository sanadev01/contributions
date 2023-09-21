@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header"> --}}
                @section('title', 'Commission Reports')
                {{-- <h4 class="mb-0">Commission Reports</h4> --}}
                {{-- </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <livewire:reports.commission-show :user="$user" />
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

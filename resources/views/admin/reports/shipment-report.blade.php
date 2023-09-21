@extends('layouts.master')
@section('css')
    <style>
        td.details-control {
            background-image: url("{{ URL::asset('images/plus.png') }}");
            cursor: pointer;
            background-repeat: no-repeat;
            background-position: center;
        }

        tr.shown td.details-control {
            background-image: url("{{ URL::asset('images/minus.png') }}");
        }
    </style>
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-0">
                        <div class="col-12 d-flex justify-content-end">
                        @section('title', __('Shipment Report'))
                        <button onclick="toggleLogsSearch()" class="btn btn-primary waves-effect mr-1 waves-light">
                            <i class="feather icon-search"></i>
                        </button>
                        {{-- <button type="btn" onclick="toggleDownloadsDiv()" id="customSwitch8"
                        class="btn btn-success mr-1 waves-effect waves-light"><i class="fa fa-arrow-down"></i></button> --}}
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                            class="btn btn-success mr-1 waves-effect waves-light"><i
                                class="fa fa-arrow-down"></i></button>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <livewire:reports.user-shipment-report-table />
                    </div>
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
    function format() {
        return '<table cellpadding="5" cellspacing="0" border="0" class="tbodyrow" style="padding-left:50px; background: #d8d8d878;">' +
            '<tr>' +
            '<th>Min Weight</th>' +
            '<th>Max Weight</th>' +
            '<th>Order</th>' +
            '</tr>' +
            '</table>';
    }
    $(document).ready(function() {
        var table = $('#example').DataTable({
            "searching": false,
            paging: false,
            "ordering": false
        });
        // Add event listener for opening and closing details
        $('#example tbody').on('click', 'td.details-control', function() {
            var id = $(this).closest("tr").find(".user_id").val();
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.ajax({
                    url: "{{ route('admin.reports.user-shipments.create') }}",
                    type: 'GET',
                    data: {
                        id: id
                    },
                    dataType: 'JSON',
                    success: function(result) {
                        row.child(format(result)).show();
                        tr.addClass('shown');
                        result.forEach(function(entry) {
                            var tdata = '<tr>' +
                                '<td>' + entry.min_weight + ' kg (' + parseFloat(
                                    entry.min_weight * 2.205).toFixed(2) + ' lbs)' +
                                '</td>' +
                                '<td>' + entry.max_weight + ' kg (' + parseFloat(
                                    entry.max_weight * 2.205).toFixed(2) + ' lbs)' +
                                '</td>' +
                                '<td>' + entry.orders + '</td>' +
                                '</tr>';
                            $('.tbodyrow').append(tdata);
                        });
                    }
                });
            }
        });
    });
</script>
@endsection

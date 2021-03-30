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
            background-image: url("{{ URL::asset('images/minus.png')}}");
        }
    </style>
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Shipment Report</h4>
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
@section('js')
    <script>
        /* Formatting function for row details - modify as you need */
        function format ( data ) {
            // `d` is the original data object for the row
                
            var html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                
                '<tr>'+
                    '<th>Min Weight</th>'+
                    '<th>Max Weight</th>'+
                    '<th>Order</th>'+
                    
                '</tr>'+
                '<tbody id="result">'+
                   
                '</tbody>'+
            '</table>';
            data.forEach(function(entry) {      
                var tdata = '<td>'+data.max_weight+'</td>'+
                '<td>'+data.min_weight+'</td>'+
                '<td>'+data.min_weight+'</td>';
                
            $('#tbody').append(tdata);
            });
        }
        
        $(document).ready(function() {

            var table = $('#example').DataTable( {
                "searching": false,
                paging: false,
                "ordering": false
            } );
            
            // Add event listener for opening and closing details
            $('#example tbody').on('click', 'td.details-control', function () {
                var id = $(this).closest("tr").find(".user_id").val();
                var tr = $(this).closest('tr');
                var row = table.row( tr );
               
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    $.ajax({
                        url: "{{route('admin.reports.user-shipments.create')}}",
                        type: 'GET',
                        data: {id:id},
                        dataType: 'JSON',
                        success: function (result) {
                            row.child( format(result) ).show();
                            tr.addClass('shown');
                        }
                    });
                }
            } );
        } );

    </script>
@endsection
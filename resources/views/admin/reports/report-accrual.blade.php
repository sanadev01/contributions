@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">

@endsection
@section('page')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section id="dashboard-analytics">
    <div class='row no-gutters d-flex align-items-center light-green-color'>
            <div class="col-xl-4 col-lg-12 light-green-color">
                <div class="light-green-color welcome-admin height-100">
                    <div class="ml-3">
                        <dl>
                            <div class="font-weight-bold large-heading-text pt-3 "> Generator Report
                            </div>
                            <dd class="font-weight-light pb-2 mb-4">Your tax & Duty report is here.</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-12 light-green-color  border-radius-15 p-2">
                <div class="row mt-0 ">
                    <div class="col-12 pb-xl-2 pb-1 h-25">
                    <div class="filter-card " id="filter-card"> 
                                    <form   action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                                        <div class="row">
                                            @csrf 

                                            <div class="col-5">
                                                <label for="end-date" class="mt-4 mb-2 font-black"><strong>Start Date</strong></label><br>
                                                <div class="input-group">
                                                    <input name="start_date" id="startDate" class="form-control py-2 mr-1 p-3" type="date">
                                                </div>
                                            </div> 
                                            <div class="col-5">
                                                <label for="end-date" class="mt-4 mb-2 font-black"><strong>End Date</strong></label><br>
                                                <div class="input-group">
                                                    <input name="end_date" id="endDate" class="form-control py-2 mr-1 p-3" type="date">
                                                </div>
                                            </div>

                                            <div class="col-2">
                                                <div class="mt-4 mb-2 font-black align-center float-center"> 
                                                    <input type="hidden" name="type" value="accrual">  
                                                    <button type="submit" class="btn btn-success waves-effect waves-light p-3 mt-4" 
                                                        {{ true ? '' : 'disabled' }}> <i class="fa fa-download"></i>  Download 
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                    </div>
                </div>
            </div>
    </div>


    
            <livewire:order.accrual-table />
                @include('layouts.livewire.loading')
          
</section>
@endsection
@section('modal')
<x-modal />
@endsection
@section('js')
<script>
    $(document).ready(function() {
            $('#kpi-report tfoot th').each(function() {
                var title = $(this).text();
                $(this).html(
                    '<input id="tableInput" type="text" class="form-control py-4" placeholder="Search ' +
                    title +
                    '" />');
            });
            var table = $('#kpi-report').DataTable({
                "paging": false,
                initComplete: function() {
                    this.api()
                        .columns()
                        .every(function() {
                            var that = this;
                            $('input', this.footer()).on('keyup change clear', function() {
                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                    calculation();
                                }
                            });
                        });
                },
                "info": false
            });
            document.getElementById("kpiHead").style.backgroundColor = "#eefafa";
            document.getElementById("kpiHeadSearch").style.backgroundColor = "#eefafa";

            calculation();
        });

        function calculation() {
            var totalRecords = $('#kpi-report tbody').find('tr.count').length;
            var taxed = 0;
            var returned = 0;
            var delivered = 0;
            var inProcess = 0;
            $(".count").each(function() {

                if ($(this).find('td').eq(8).text().trim() == 'Yes') {
                    taxed++;
                }
                if ($(this).find('td').eq(9).text().trim() == 'Yes') {
                    delivered++;
                }
                if ($(this).find('td').eq(10).text().trim() == 'Yes') {

                    returned++;
                }
                if ($(this).find('td').eq(9).text().trim() == 'No') {
                    inProcess++;
                }
            });
            var taxOrder = (taxed / totalRecords * 100).toFixed(2);
            var deliveredOrder = (delivered / totalRecords * 100).toFixed(2);
            var returnOrder = (returned / totalRecords * 100).toFixed(2);
            var inTransit = (inProcess / totalRecords * 100).toFixed(2);
            $('#total').html(totalRecords);
            if(!isNaN(deliveredOrder)){ 
             $('#delivered').html(deliveredOrder + ' %');
            }
            if(!isNaN(taxOrder)){ 
            $('#taxed').html(taxOrder + ' %');
            }
            if(!isNaN(returnOrder)){  
            $('#returned').html(returnOrder + ' %');
            }
            $('#inProcess').html('Processing or In Transit: ' + inTransit + ' %');
            document.getElementById("kpiHead").style.backgroundColor = "#eefafa"
            document.getElementById("kpiHeadSearch").style.backgroundColor = "#eefafa";

        }
</script>
@endsection
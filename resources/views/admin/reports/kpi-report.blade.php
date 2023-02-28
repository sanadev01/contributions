@extends('layouts.master')
@section('css')
    <style>
        .dataTables_filter {
            display: none;
        }
    </style>
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('orders.Key Performance Indicator Report')</h4>
                    </div><br>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row col-12">
                                <div class="col-md-4 p-0">
                                    <h5>Search Per Date Range</h5>
                                </div>
                                <div class="col-md-3 ml-0">
                                    <h5>Track Tracking Multiple</h5>
                                </div>
                            </div>
                            <div class="row mb-4 no-print">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form class="row col-12 ustify-content-end" action="{{ route('admin.reports.kpi-report.index') }}" method="GET">
                                                @csrf
                                                <label class="mt-1 mr-3">Start Date</label>
                                                <input type="date" name="start_date" id="startDate" placeholder="mm/dd/yyyy" class="form-control col-2 mr-5">

                                                <label class="mt-1 mr-3">End Date</label>
                                                <input type="date" name="end_date" id="endDate" placeholder="mm/dd/yyyy" class="form-control col-2 mr-5">

                                                <div class="col-md-4">
                                                    <div class="col-12 p-0">
                                                        <div class="controls">
                                                            <div class="col-md-12 ml-0 pl-0 pr-0">
                                                                <textarea type="text" placeholder="Please Enter Tracking Codes" rows="3" 
                                                                class="form-control"
                                                                    name="trackingNumbers">{{ old('trackingNumbers',request('trackingNumbers')) }}</textarea>
                                                                @error('trackingNumbers')
                                                                    <div class="help-block text-danger"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 mt-5">
                                                    <button class="btn btn-primary btn-md">
                                                        @lang('user.Search')
                                                    </button>
                                                </div>
                                            </form> 
                                        </div>
                                        <div class="col-md-1 mt-5">
                                            <form class="row col-12 ustify-content-end" action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                                                @csrf
                                                @if($trackings)
                                                    <input type="hidden" name="order" value="{{ collect($trackings['return']['objeto']) }}">
                                                    <input type="hidden" name="trackingCodeUser" value="{{ collect($trackingCodeUser) }}">
                                                @endif   
                                                <button class="btn btn-success m-0" {{ !empty($trackings)? '' : 'disabled' }}  title="@lang('orders.import-excel.Download')">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="col-md-3 mt-3">
                                            <div class="row col-12">
                                                <div class="col-6">
                                                    <h4><span class="p-2 col-12 badge badge-primary mr-2 " id="total">Total Orders</span></h4>
                                                    <h4><span class="p-2 col-12 badge badge-success mr-2 text-dark" id="delivered">Delivered</span></h4>
                                                </div>
                                                <div class="col-6">
                                                    <h4><span class="p-2 badge badge-info mr-2 text-dark col-12" id="taxed">Taxed</span></h4>
                                                    <h4><span class="p-2 badge badge-danger mr-2 col-12" id="returned">Returned</span></h4>
                                                </div>
                                                <div class="col-12">
                                                    <h4><span class="p-2 badge badge-secondary mr-2 text-dark col-12" id="inProcess">Processing or In Transit</span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table mb-0 table-responsive-md" id="kpi-report">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>@lang('orders.Tracking')</th>
                                        <th>@lang('orders.Type Package')</th>
                                        <th>@lang('orders.First Event')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Days Between')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Taxed')</th>
                                        <th>@lang('orders.Delivered')</th>
                                        <th>@lang('orders.Returned')</th>
                                    </tr>
                                </thead>
                                <tfoot class="search-header">
                                    <tr>
                                        <th>User</th>
                                        <th>Tracking</th>
                                        <th>Type Package</th>
                                        <th>First Event</th>
                                        <th>Last Event</th>
                                        <th>Days Between</th>
                                        <th>Last Event</th>
                                        <th>Taxed</th>
                                        <th>Delivered</th>
                                        <th>Returned</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @if($trackings)
                                        @foreach($trackings['return']['objeto'] as $data)
                                            @if(isset($data['evento']))
                                            <tr>
                                                @if(optional($data) && isset(optional($data)['numero']))
                                                    <td>{{ optional($trackingCodeUser[optional($data)['numero']])->pobox_name }}</td>
                                                    <td>{{ optional($data)['numero'] }}</td>
                                                    <td><span>{{ optional($data)['categoria'] }}</span></td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[count($data['evento'])-1])['data'] }}</td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[0])['data'] }}</td>
                                                    <td>{{ sortTrackingEvents($data, null)['diffDates'] }} </td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[0])['descricao'] }}</td>
                                                    <td>{{ sortTrackingEvents($data, null)['taxed'] }}</td>
                                                    <td>{{ sortTrackingEvents($data, null)['delivered'] }}</td>
                                                    <td>{{ sortTrackingEvents($data, null)['returned'] }}</td>
                                                @else
                                                <td colspan='9'>No Trackings Found</td>
                                                @endif
                                            </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @include('layouts.livewire.loading')
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
        $(document).ready(function () {
            $('#kpi-report tfoot th').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            });
            var table = $('#kpi-report').DataTable({
                "paging": false,
                initComplete: function () {
                    this.api()
                        .columns()
                        .every(function () {
                            var that = this;
        
                            $('input', this.footer()).on('keyup change clear', function () {
                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                    calculation();
                                }
                            });
                        });
                },
                "info": false
            });
            calculation();
        });
        
        function calculation() {
            var totalRecords = $('#kpi-report tbody').find('tr').length;
            var taxed = 0;
            var returned = 0;
            var delivered = 0;
            var inProcess = 0;
            $("table > tbody > tr").each(function () {
                if($(this).find('td').eq(7).text() == 'Yes' ){
                    taxed++;  
                }
                if($(this).find('td').eq(8).text() == 'Yes' ){
                    delivered++;  
                }
                if($(this).find('td').eq(9).text() == 'Yes' ){
                    returned++;  
                }
                if($(this).find('td').eq(8).text() == 'No'){
                    inProcess++;  
                }
            });
            var taxOrder = (taxed / totalRecords * 100).toFixed(2);
            var deliveredOrder = (delivered / totalRecords * 100).toFixed(2);
            var returnOrder = (returned / totalRecords * 100).toFixed(2); 
            var inTransit = (inProcess / totalRecords * 100).toFixed(2);
            $('#total').html('Total Orders: '+totalRecords);
            $('#delivered').html('Delivered: '+ deliveredOrder + ' %');
            $('#taxed').html('Taxed: '+ taxOrder + ' %');
            $('#returned').html('Returned: '+ returnOrder + ' %');
            $('#inProcess').html('Processing or In Transit: '+ inTransit + ' %');
        }
    </script>
@endsection

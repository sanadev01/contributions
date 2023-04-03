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
                            <div class="row mb-4 no-print">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="row col-lg-9">
                                            <div class="col-lg-10 col-sm-12">
                                                <div class="row pr-0 col-12 mb-1">
                                                    <div class="col-md-4 p-0 ml-4">
                                                        <h5>Search Per Date Range</h5>
                                                    </div>
                                                </div>
                                                <form class="row col-12 m-0 pr-0"
                                                    action="{{ route('admin.reports.kpi-report-scan.index') }}" method="GET">
                                                    
                                                    <div class="col-lg-5 col-md-5 ">
                                                        <div class="row">
                                                            
                                                        <div class="col-xl-6 col-lg-12 col-lg-6 col-lg-12 ">
                                                            <label class="">Start Date</label>
                                                            <input type="date" name="start_date" id="startDate" value="{{request()->start_date}}"
                                                                placeholder="yyyy-mm-dd" class="form-control ">
                                                        </div>
                                                        <div class="col-xl-6 col-lg-12 col-lg-6 col-lg-12">
                                                            <label>End Date</label>
                                                            <input type="date" name="end_date" id="endDate" value="{{request()->start_date}}"
                                                                placeholder="yyyy-mm-dd" class="form-control">

                                                        </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-lg-5 col-lg-4">
                                                        <div class="controls">
                                                            <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                                            <livewire:components.search-user selectedId="{{request('user_id')}}" />
                                                            @error('pobox_number')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2  p-0   d-flex align-items-end  mt-3 m-0">
                                                        <button class="btn btn-primary btn-md p-2">
                                                            @lang('user.Search')
                                                        </button>
                                                    </div>

                                                </form>


                                            </div>
                                            <div class="col-lg-2 col-sm-12 d-flex align-items-end justify-content-center">
                                                <form class="row col-12 pl-0"
                                                    action="{{ route('admin.reports.kpi-report-scan.store') }}" method="POST">
                                                    @csrf
                                                    @if ($trackings)
                                                        <input type="hidden" name="order"
                                                            value="{{ collect($trackings['return']['objeto']) }}">
                                                        <input type="hidden" name="trackingCodeUser"
                                                            value="{{ collect($trackingCodeUser) }}">
                                                        <input type="hidden" name="orderDates"
                                                            value="{{ collect($orderDates) }}">
                                                    @endif
                                                    <button class="btn btn-success mr-3 mt-3"
                                                        {{ !empty($trackings) ? '' : 'disabled' }}
                                                        title="@lang('orders.import-excel.Download')">
                                                        <i class="fa fa-arrow-down"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 d-flex align-items-end justify-content-center">
                                            <div class="row col-12">
                                                <div class="col-6 col-xl-6 col-lg-12  col-md-6  col-sm-12">
                                                   <span class="p-1 display-3 col-12 badge badge-primary mr-2 my-2"
                                                            id="total">Total Orders</span> 
                                                     <span class="p-1 display-3  col-12 badge badge-success mr-2 my-2 text-dark"
                                                            id="delivered">Delivered</span> 
                                                </div>
                                                <div class="col-6 col-xl-6 col-lg-12  col-md-6  col-sm-12">
                                                   <span class="p-1 display-3  badge badge-info mr-2 text-dark my-2 col-12"
                                                            id="taxed">Taxed</span> 
                                                     <span class="p-1 display-3 badge badge-danger mr-2 my-2 col-12"
                                                            id="returned">Returned</span> 
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <span class="p-1 display-3 badge badge-secondary mr-2 text-dark col-12"
                                                            id="inProcess">Processing or In Transit</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table mb-0 table-responsive-md" id="kpi-report">
                                <thead >
                                    <tr>
                                        <th>Order Date</th>
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
                                        <th>Order Date</th>
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
                                            @if(isset($data['evento']) && optional(optional(optional($data)['evento'])[0])['descricao']=='Aguardando pagamento')
                                            <tr class="count">
                                                @if(optional($data) && isset(optional($data)['numero']))
                                                    <td>{{ optional($orderDates[optional($data)['numero']]) }}</td>
                                                    <td>{{ optional($trackingCodeUser[optional($data)['numero']]) }}</td>
                                                    <td>{{ optional($data)['numero'] }}</td>
                                                    <td><span>{{ optional($data)['categoria'] }}</span></td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[count($data['evento'])-1])['data'] }}</td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[0])['data'] }}</td>
                                                    <td>{{ sortTrackingEvents($data, null)['diffDates'] }} </td>
                                                    <td>{{ optional(optional(optional($data)['evento'])[0])['descricao'] }} </td>
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
        $(document).ready(function() {
            $('#kpi-report tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title +
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
            calculation();
        });

        function calculation() {
            var totalRecords = $('#kpi-report tbody').find('tr.count').length;
            var taxed = 0;
            var returned = 0;
            var delivered = 0;
            var inProcess = 0;
            $("table > tbody > tr").each(function() {
                if ($(this).find('td').eq(8).text() == 'Yes') {
                    taxed++;
                }
                if ($(this).find('td').eq(9).text() == 'Yes') {
                    delivered++;
                }
                if ($(this).find('td').eq(10).text() == 'Yes') {
                    returned++;
                }
                if ($(this).find('td').eq(9).text() == 'No') {
                    inProcess++;
                }
            });
            var taxOrder = (taxed / totalRecords * 100).toFixed(2);
            var deliveredOrder = (delivered / totalRecords * 100).toFixed(2);
            var returnOrder = (returned / totalRecords * 100).toFixed(2);
            var inTransit = (inProcess / totalRecords * 100).toFixed(2);
            $('#total').html('Total Orders: ' + totalRecords);
            $('#delivered').html('Delivered: ' + deliveredOrder + ' %');
            $('#taxed').html('Taxed: ' + taxOrder + ' %');
            $('#returned').html('Returned: ' + returnOrder + ' %');
            $('#inProcess').html('Processing or In Transit: ' + inTransit + ' %');
        }
    </script>
@endsection

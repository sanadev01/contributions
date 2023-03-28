@extends('layouts.master')
@section('css')
    <style>
        .dataTables_filter {
            display: none;
        }

        .wrimagecard {
            border: 1px solid rgba(46, 61, 73, 0.15);
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
            background: #fff;
            /* box-shadow:2px 4px 4px 2px  rgba(46,61,73,0.15); */
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .wrimagecard .fa {
            position: relative;
            font-size: 50px;
            padding: 10px;

        }

        .wrimagecard-topimage_header {
            padding: 10px;
        }

        a.wrimagecard:hover,
        .wrimagecard-topimage:hover {
            box-shadow: 0px 0px 6px 1px rgba(46, 61, 73, 0.2);
        }

        .wrimagecard-topimage a {
            width: 100%;
            height: 100%;
            display: block;
        }

        .wrimagecard-topimage a {
            border-bottom: none;
            text-decoration: none;
            color: #525c65;
            transition: color 0.3s ease;
        }

        .icon-color {
            border-radius: 11px;

        }

        .btn {
            border-radius: 11px;
        }

        .banner {
            border-radius: 11px;
        }

        .filter-card {
            padding: 12px 25px;
            border: 1px solid rgba(46, 61, 73, 0.15);
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
            background: #fff;
            box-shadow: 0px 0px 40px 1px rgba(120, 148, 171, 0.2);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        #filter-card:hover {
            box-shadow: 2px 4px 12px 6px rgba(46, 61, 73, 0.2);
        }

        thead {
            background-color: #EEFAFA !important;
        } 
        .table-striped>tbody>tr:nth-child(odd)>td,
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: #fff;
        }
        body {
             background-color:#F7FBFE !important;
        }
    </style>
@endsection
@section('page')
    <section >
        <div class="row">
            <div class="col-12">
                <div class="">
                    <div>
                        <dl>
                            <dt class="h3 font-weight-bold">Welcome back , Marcio 👋</dt>
                            <dd class="display-5 font-weight-light">Your current kpi report is here</dd>
                        </dl>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-8">
                                {{-- contanct us banner --}}
                                <div class="row">
                                    <div class="">
                                        <div style="col-sm-12 position:relative;">
                                            <div class="">
                                                <div class="col-12 mt-3" style="height:7%" class="mt-3">
                                                    <img class="banner" src="{{ asset('images/kpi-banner.png') }}"
                                                        width="100%" height="10%" alt="your-image">
                                                    {{-- <div style="position:absolute;top:0;left:0;vertical-align: middle" class="d-flex justify-content-between align-items-center">
                                                        
                                                            <div class="  "> 
                                                                <h4 class="text-left">Get Help from our support staff</h4>
                                                            </div> 
                                                            <div class=" ">

                                                                <button>Contact Us</button>
                                                            </div>  
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- contanct us banner end --}}
                                {{-- orders details cards --}}
                                <div class="row mt-5">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="wrimagecard wrimagecard-topimage">
                                            <a href="#">
                                                <div class="wrimagecard-topimage_header">
                                                    <div class="icon-color" style="background-color: #EEFAFA">
                                                        <center> <i class="fa fa-cubes" style="color:#284ed8"></i> </center>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3">123</p>
                                                    <p class="text-center mt-0 pt-0">Total orders</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="wrimagecard wrimagecard-topimage">
                                            <a href="#">
                                                <div class="wrimagecard-topimage_header">
                                                    <div class="icon-color" style="background-color: #FFF9EB">
                                                        <center> <i class="fa fa-cubes" style="color:#a08e16"></i> </center>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3">23 </p>
                                                    <p class="text-center mt-0 pt-0">Taxed</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="wrimagecard wrimagecard-topimage">
                                            <a href="#">
                                                <div class="wrimagecard-topimage_header">
                                                    <div class="icon-color" style="background-color: #EEFAFA">
                                                        <center><i class="fa fa-cubes" style="color:#16A085"></i> </center>
                                                    </div>
                                                </div>
                                                <div>

                                                    <p class="text-center font-weight-bold h3"> 123
                                                    </p>

                                                    <p class="text-center mt-0 pt-0">Delivered</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="wrimagecard wrimagecard-topimage">
                                            <a href="#">
                                                <div class="wrimagecard-topimage_header">
                                                    <div class="icon-color" style="background-color: #FBEFFB">
                                                        <center><i class="fa fa-cubes" style="color:#dc341b"></i>
                                                        </center>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3"> 61
                                                    </p>
                                                    <p class="text-center mt-0 pt-0">Retured</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                {{-- orders details cards end --}}

                            </div>
                            {{-- Report Generato --}}
                            <div class="col-4 ">
                                <div class="filter-card" id="filter-card">
                                    <h4 class="text-center mt-0 pt-0  h5 font-weight-bold">
                                        Report Generator
                                    </h4>


                                    <label for="start-date" class="mb-0">Start Date</label><br>
                                    <div class="input-group">
                                        <input id="start-date" class="form-control py-2 rounded-2 mr-1 " type="date"
                                            value="start date">

                                    </div>

                                    <label for="end-date " class="mt-2  mb-0""> End Date</label><br>
                                    <div class="input-group">
                                        <input id="end-date" class="form-control py-2 rounded-2 mr-1 " type="date"
                                            value="end date">

                                    </div>



                                    <label for="start-date" class="mt-2 mb-0">Tracking Code</label><br>
                                    <div class="input-group">
                                        <textarea id="start-date" class="form-control py-2 rounded-2 mr-1 pr-5" value="tracking code"></textarea>
                                        <span class="input-group-append">
                                            <button class="btn rounded-pill border-0 ml-n5" type="button">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">

                                        <div>
                                            <button type="button" class="btn btn-outline-success  px-3 py-1">Check
                                                Details</button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-success   px-3 py-1"> <i
                                                    class="fa fa-download"></i> Download</button>

                                        </div>
                                    </div>


                                </div>
                            </div>
                            {{-- Report Generato end --}}
                        </div>
                        {{-- table of kpi --}}
                        <div>
                            <table class="table p-0 table-responsive-md table-striped">
                                <thead style="backgroud-color:#000">
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
                                {{-- <tfoot class="search-header">
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
                                </tfoot> --}}
                                <tbody>
                                    <tr>
                                        <td>User</td>
                                        <td>Tracking</td>
                                        <td>Type Package</td>
                                        <td>First Event</td>
                                        <td>Last Event</td>
                                        <td>Days Between</td>
                                        <td>Last Event</td>
                                        <td>Taxed</td>
                                        <td>Delivered</td>
                                        <td>Returned</td>
                                    </tr>
                                    <tr>
                                        <td>User</td>
                                        <td>Tracking</td>
                                        <td>Type Package</td>
                                        <td>First Event</td>
                                        <td>Last Event</td>
                                        <td>Days Between</td>
                                        <td>Last Event</td>
                                        <td>Taxed</td>
                                        <td>Delivered</td>
                                        <td>Returned</td>
                                    </tr>
                                    <tr>
                                        <td>User</td>
                                        <td>Tracking</td>
                                        <td>Type Package</td>
                                        <td>First Event</td>
                                        <td>Last Event</td>
                                        <td>Days Between</td>
                                        <td>Last Event</td>
                                        <td>Taxed</td>
                                        <td>Delivered</td>
                                        <td>Returned</td>
                                    </tr>

                                    @if ($trackings)
                                        @foreach ($trackings['return']['objeto'] as $data)
                                            @if (isset($data['evento']))
                                                <tr class="count">
                                                    @if (optional($data) && isset(optional($data)['numero']))
                                                        <td>{{ optional($trackingCodeUser[optional($data)['numero']])->pobox_name }}
                                                        </td>
                                                        <td>{{ optional($data)['numero'] }}</td>
                                                        <td><span>{{ optional($data)['categoria'] }}</span></td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[count($data['evento']) - 1])['data'] }}
                                                        </td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[0])['data'] }}
                                                        </td>
                                                        <td>{{ sortTrackingEvents($data, null)['diffDates'] }} </td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[0])['descricao'] }}
                                                        </td>
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


                            {{-- <div class="row d-flex justify-content-between">
                                    <div>User</div>      
                                     <div>@lang('orders.Tracking')</div>
                                    <div>@lang('orders.Type Package')</div>
                                    <div>@lang('orders.First Event')</div>
                                    <div>@lang('orders.Last Event')</div>
                                    <div>@lang('orders.Days Between')</div>
                                    <div>@lang('orders.Last Event')</div>
                                    <div>@lang('orders.Taxed')</div>
                                    <div>@lang('orders.Delivered')</div>
                                    <div>@lang('orders.Returned')</div>
 
                                </div>
                                
                                        <div class="row d-flex justify-content-between">

                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                        <div>1</div>
                                    
                                        <div>1</div>
                                        </div> --}}




                        </div>

                    </div>
                </div>
                {{-- <div class="card">
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
                                                    action="{{ route('admin.reports.kpi-report.index') }}" method="GET">

                                                    <div class="col-lg-5 col-md-5 ">
                                                        <div class="row">

                                                            <div class="col-xl-6 col-lg-12 col-lg-6 col-lg-12 ">
                                                                <label class="">Start Date</label>
                                                                <input type="date" name="start_date" id="startDate"
                                                                    placeholder="mm/dd/yyyy" class="form-control ">
                                                            </div>
                                                            <div class="col-xl-6 col-lg-12 col-lg-6 col-lg-12">
                                                                <label>End Date</label>
                                                                <input type="date" name="end_date" id="endDate"
                                                                    placeholder="mm/dd/yyyy" class="form-control">

                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-5 col-lg-4">
                                                        <h5 class="my-2">Track Tracking Multiple</h5>
                                                        <div class="col-12 p-0">
                                                            <div class="controls">
                                                                <div class="col-md-12 ml-0 pl-0 pr-0">
                                                                    <textarea type="text" placeholder="Please Enter Tracking Codes" rows="3" class="form-control"
                                                                        name="trackingNumbers">{{ old('trackingNumbers', request('trackingNumbers')) }}</textarea>
                                                                    @error('trackingNumbers')
                                                                        <div class="help-block text-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
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
                                                    action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                                                    @csrf
                                                    @if ($trackings)
                                                        <input type="hidden" name="order"
                                                            value="{{ collect($trackings['return']['objeto']) }}">
                                                        <input type="hidden" name="trackingCodeUser"
                                                            value="{{ collect($trackingCodeUser) }}">
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
                                                    <span
                                                        class="p-1 display-3  col-12 badge badge-success mr-2 my-2 text-dark"
                                                        id="delivered">Delivered</span>
                                                </div>
                                                <div class="col-6 col-xl-6 col-lg-12  col-md-6  col-sm-12">
                                                    <span
                                                        class="p-1 display-3  badge badge-info mr-2 text-dark my-2 col-12"
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
                            <table class="table p-0 table-responsive-md" >
                                <thead style="backgroud-color:#000">
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
                                    @if ($trackings)
                                        @foreach ($trackings['return']['objeto'] as $data)
                                            @if (isset($data['evento']))
                                                <tr class="count">
                                                    @if (optional($data) && isset(optional($data)['numero']))
                                                        <td>{{ optional($trackingCodeUser[optional($data)['numero']])->pobox_name }}
                                                        </td>
                                                        <td>{{ optional($data)['numero'] }}</td>
                                                        <td><span>{{ optional($data)['categoria'] }}</span></td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[count($data['evento']) - 1])['data'] }}
                                                        </td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[0])['data'] }}
                                                        </td>
                                                        <td>{{ sortTrackingEvents($data, null)['diffDates'] }} </td>
                                                        <td>{{ optional(optional(optional($data)['evento'])[0])['descricao'] }}
                                                        </td>
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
                </div> --}}
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
                if ($(this).find('td').eq(7).text() == 'Yes') {
                    taxed++;
                }
                if ($(this).find('td').eq(8).text() == 'Yes') {
                    delivered++;
                }
                if ($(this).find('td').eq(9).text() == 'Yes') {
                    returned++;
                }
                if ($(this).find('td').eq(8).text() == 'No') {
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

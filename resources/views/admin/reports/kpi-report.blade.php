@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">

@endsection
@section('page')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section>
    <div class="row mt-4">
        <div class="col-12 mx-2">
            <div>
                <div class="ml-3">
                    <dl>
                        <dt class="font-weight-bold dt">Welcome back, {{ Auth::user()->name }} 👋</dt>
                        <dd class="display-5 my-3 font-weight-light pb-2 mb-5">Your current kpi report is here</dd>
                    </dl>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8 col-sm-12 d-flex flex-column justify-content-between">
                            {{-- contanct us banner --}}
                            <div>
                                <div class="row mt-0">
                                    <div class="col-12 pb-xl-2 pb-1 h-25">
                                        <a href="{{ url('tickets') }}" target="_blank"> <img class="banner"
                                                src="{{ asset('images/kpi-banner.png') }}" width="100%" height="auto"
                                                alt="contact us"> 
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {{-- contanct us banner end --}}
                            {{-- orders details cards --}}
                            <div class="row d-flex justify-content-between">
                                <div class="col-md-3 col-sm-6 custom-cards ">
                                    <div class="card imagecard imagecard-topimage pr-lg-2 mr-lg-2">
                                        <div class="icon-background m-4 p-5 d-flex justify-content-center"
                                            style="background-color: #EEFAFA">
                                            <img src="{{ asset('app-assets/images/icons/chart.svg') }}" class="icon"
                                                width="60" height="60" />
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <h3 class="text-center font-weight-bold my-1 font-black" id="total">  0 </h3>
                                                <p class="text-center display-5 font-black"><strong>Total Orders</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6  custom-cards">
                                    <div class="card imagecard imagecard-topimage">
                                        <div class="icon-background m-4 p-5 d-flex justify-content-center"
                                            style="background-color: #fff9eb">
                                            <img src="{{ asset('app-assets/images/icons/tax.svg') }}" class="icon" width="60" height="60" />
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <h3 class="text-center font-weight-bold my-1 font-black" id="taxed"> 0 </h3>
                                                <p class="text-center display-5 font-black"><strong>Taxed</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6  custom-cards">
                                    <div class="card imagecard imagecard-topimage">
                                        <div class="icon-background m-4 p-5 d-flex justify-content-center"
                                            style="background-color: #eefafa">
                                            <img src="{{ asset('app-assets/images/icons/delivered.svg') }}" class="icon"
                                                width="60" height="60" />
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <h3 class="text-center font-weight-bold my-1 font-black" id="delivered"> 0 </h3>
                                                <p class="text-center display-5 font-black"><strong>Delivered</strong> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6  custom-cards ">
                                    <div class="card imagecard imagecard-topimage ml-xl-2 pl-xl-2">
                                        <div class="icon-background m-4 p-5 d-flex justify-content-center"
                                            style="background-color: #fbeffb">
                                            <img src="{{ asset('app-assets/images/icons/returned.svg') }}" class="icon"
                                                width="60" height="60" />
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <h3 class="text-center font-weight-bold my-1 font-black" id="returned"> 0 </h3>
                                                <p class="text-center display-5 font-black"><strong>Returned</strong> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- orders details cards end --}}
                        </div>
                        {{-- Report Report --}}
                        <div class=" col-lg-4 col-sm-12 d-flex flex-column justify-content-center ">
                            <div class="filter-card " id="filter-card">
                                <h4 class="text-center m-4 font-weight-bold font-black">  Report Generator </h4>
                                <form action="{{ route('admin.reports.kpi-report.index') }}" method="GET">
                                    <label for="startDate " class="mt-3 mb-2 font-black"><strong>Start Date</strong></label><br>
                                    <div class="input-group">
                                        <input class="form-control py-2 mr-1 p-3" type="date" name="start_date" id="startDate">
                                    </div>
                                    <label for="end-date" class="mt-4 mb-2 font-black"><strong>End Date</strong></label><br>
                                    <div class="input-group">
                                        <input name="end_date" id="endDate" class="form-control py-2 mr-1 p-3" type="date">
                                    </div>
                                    @if (request('type')=='scan')
                                    <input name="type"  type="hidden" value="scan">

                                        <label for="tracking_code" class="mt-4 mb-2 font-black"><strong>@lang('parcel.User POBOX Number')</strong></label><br>
                                        <div class="input-group w-100">
                                            <livewire:components.search-user selectedId="{{ request('user_id') }}"/>
                                            @error('pobox_number')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    @else
                                    <label for="tracking_code" class="mt-4 mb-2 font-black"><strong>Tracking  Code</strong></label><br>
                                    <div class="input-group">
                                        <input name="type"  type="hidden" value="report">
                                        <textarea id="tracking_code" value="tracking code" type="text" placeholder="Please Enter Tracking Codes" 
                                                  rows="2" class="form-control py-2 mr-1 rounded-lg"
                                                  name="trackingNumbers">{{ old('trackingNumbers', request('trackingNumbers')) }}</textarea>
                                        @error('trackingNumbers')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                        <span class="input-group-append">
                                            <button class="btn rounded-pill border-0 ml-n5" type="button"> <i class="fa fa-search"></i> </button>
                                        </span>
                                    </div>
                                    @endif 
                                    <div class="d-flex justify-content-between mt-4 mb-3">
                                        <div>
                                            <button type="submit"
                                                class="btn btn-outline-success glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                                Check Details
                                            </button>
                                        </div>
                                    <div>
                                </form>
                                <form class="row col-12 p-0 m-0" action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                                    @csrf
                                    @if ($trackings)
                                        <input type="hidden" name="order" value="{{ collect($trackings['return']['objeto']) }}">
                                        <input type="hidden" name="type" value="{{ request('type') }}">
                                        <input type="hidden" name="trackingCodeUsersName" value="{{ collect($trackingCodeUsersName) }}">
                                        <input type="hidden" name="orderDates" value="{{ collect($orderDates) }}">
                                    @endif
                                    <button type="submit" class="btn btn-success waves-effect waves-light p-3" 
                                         {{ !empty($trackings) ? '' : 'disabled' }}> <i class="fa fa-download"></i>  Download 
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Report Generato end --}}
            </div>
            {{-- table of kpi --}}
            <div>
                <table class="table  table-borderless p-0 table-responsive-md table-striped  " id="kpi-report">
                    <thead>
                        <tr id="kpiHead">
                            <th class="py-3 font-black">Order Date</th>
                            <th class="py-3 font-black">User</th>
                            <th class="py-3 font-black">@lang('orders.Tracking')</th>
                            <th class="py-3 font-black">@lang('orders.Type Package')</th>
                            <th class="py-3 font-black">@lang('orders.First Event')</th>
                            <th class="py-3 font-black">@lang('orders.Last Event')</th>
                            <th class="py-3 font-black">@lang('orders.Days Between')</th>
                            <th class="py-3 font-black">@lang('orders.Last Event')</th>
                            <th class="py-3 font-black">@lang('orders.Taxed')</th>
                            <th class="py-3 font-black">@lang('orders.Delivered')</th>
                            <th class="py-3 font-black">@lang('orders.Returned')</th>
                        </tr>
                    </thead>
                    <tfoot class="search-header">
                        <tr id="kpiHeadSearch">
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
                        <tbody>
                            @if ($trackings)
                            @foreach ($trackings['return']['objeto'] as $data)
                                @if (isset($data['evento']))
                                        @if (request('type')=='scan' && optional(optional(optional($data)['evento'])[0])['descricao'] != 'Aguardando pagamento')
                                            @continue
                                        @endif
                                        <tr class="count">
                                            @if (optional($data) && isset(optional($data)['numero']))
                                                <td>
                                                    <p class="center-text"> {{ $orderDates[optional($data)['numero']] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ $trackingCodeUsersName[optional($data)['numero']] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text">{{ optional($data)['numero'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> <span>{{ optional($data)['categoria'] }}</span> </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ optional(optional(optional($data)['evento'])[count($data['evento'])-1])['data'] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ optional(optional(optional($data)['evento'])[0])['data'] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ sortTrackingEvents($data, null)['diffDates'] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ optional(optional(optional($data)['evento'])[0])['descricao'] }} </p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ sortTrackingEvents($data, null)['taxed'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ sortTrackingEvents($data, null)['delivered'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="center-text"> {{ sortTrackingEvents($data, null)['returned'] }}</p>
                                                </td>
                                            @else
                                                <td colspan="11">
                                                    <p class="center-text">No Trackings Found</p>
                                                </td>
                                            @endif
                                        </tr>
                                @endif
                            @endforeach
                            @else
                            <tr>
                                <td colspan="11" class="text-center">No Trackings Found</td>
                            </tr>
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
@extends('layouts.master')  
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}"> 
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="">
                    <div class="ml-3">
                        <dl>
                            <dt class="h3 font-weight-bold dt">Welcome back, {{ Auth::user()->full_name }} 👋</dt>
                            <dd class="display-5 font-weight-light dd">Your current kpi report is here</dd>
                        </dl>
                    </div>
                    <div class="container-fluid">
                        <div class="row mt-3">
                            <div class="col-lg-8 col-md-12  d-flex  flex-column  justify-content-between ">
                                {{-- contanct us banner --}}
                                {{-- <div class="">

                                </div> --}}
                                <div class="">
                                    <div class="row mt-0">
                                        <div class="col-12 pb-xl-3 pb-2 h-50"><a href="#">
                                                <img class="banner" src="{{ asset('images/kpi-banner.png') }}"
                                                    width="100%" height="10%" alt="contact us"> </a>
                                        </div>
                                    </div>
                                </div>
                                {{-- contanct us banner end --}}
                                {{-- orders details cards --}}
                                <div class="row ">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="imagecard imagecard-topimage">
                                            <a href="#">
                                                <div class="imagecard-topimage_header">
                                                    <div class="icon-background" style="background-color: #EEFAFA">
                                                        <center> <img src="{{ asset('app-assets/images/icons/chart.svg') }}"
                                                                class="icon" width="40" height="40" /> </center>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3"  id="total">0</p>
                                                    <p class="text-center mt-0 pt-0">Total orders</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="imagecard imagecard-topimage">
                                            <a href="#">
                                                <div class="imagecard-topimage_header">
                                                    <div class="icon-background" style="background-color: #fff9eb">
                                                        <center> <img src="{{ asset('app-assets/images/icons/tax.svg') }}" class="icon" width="40" height="40" /> </center>

                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3"  id="taxed">0 </p>
                                                    <p class="text-center mt-0 pt-0">Taxed</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="imagecard imagecard-topimage">
                                            <a href="#">
                                                <div class="imagecard-topimage_header">
                                                    <div class="icon-background" style="background-color: #eefafa">
                                                        <center> <img
                                                                src="{{ asset('app-assets/images/icons/delivered.svg') }}"
                                                                class="icon" width="40" height="40" /> </center>

                                                    </div>
                                                </div>
                                                <div> 


                                                    <p class="text-center font-weight-bold h3 "  id="delivered"> 0
                                                    </p>

                                                    <p class="text-center mt-0 pt-0">Delivered</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="imagecard imagecard-topimage">
                                            <a href="#">
                                                <div class="imagecard-topimage_header">
                                                    <div class="icon-background" style="background-color: #fbeffb">
                                                        <center> <img
                                                                src="{{ asset('app-assets/images/icons/retured.svg') }}"
                                                                class="icon" width="40" height="40" /> </center>

                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-center font-weight-bold h3"  id="returned"> 0
                                                    </p>
                                                    <p class="text-center mt-0 pt-0">Retured</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- orders details cards end --}}

                            </div>
                            {{-- Report Report  --}}
                            <div class="col-lg-4 col-md-12  d-flex  flex-column   justify-content-center">
                                <div class="filter-card" id="filter-card">
                                    <h4 class="text-center mt-0 pt-0  h5 font-weight-bold">
                                        Report Generator
                                    </h4>
                                    <form action="{{ route('admin.reports.kpi-report.index') }}" method="GET">
                                        <label for="startDate " class="mt-xl-3 mt-lg-1 mb-0 ">Start Date</label><br>
                                        <div class="input-group">
                                            <input class="form-control py-2 rounded-3 mr-1 " type="date"
                                                name="start_date" id="startDate" {{-- value="{{ \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d')  }}" placeholder="yyyy/dd/mm" --}}>
                                        </div>
                                        <label for="end-date" class="mt-xl-3 mt-lg-1 mb-0 ""> End Date</label><br>
                                        <div class="input-group">
                                            <input name="end_date" id="endDate" class="form-control rounded-5"
                                                type="date">
                                        </div>
                                        <label for="start-date" class="mt-xl-3 mt-lg-1 mb-0 ">Tracking Code</label><br>
                                        <div class="input-group">
                                            <textarea id="start-date" value="tracking code" type="text" placeholder="Please Enter Tracking Codes" rows="1"
                                                class="form-control rounded-3" name="trackingNumbers">{{ old('trackingNumbers', request('trackingNumbers')) }}</textarea>
                                            @error('trackingNumbers')
                                                <div class="help-block text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <span class="input-group-append">
                                                <button class="btn rounded-pill border-0 ml-n5" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <div>
                                                <button type="submit" class="btn btn-outline-success px-3 py-1">Check
                                                    Details</button>
                                            </div>
                                            <div>
                                    </form>
                                    <form class="row col-12 p-0 m-0" action="{{ route('admin.reports.kpi-report.store') }}"
                                        method="POST">
                                        @csrf
                                        @if ($trackings)
                                            <input type="hidden" name="order"
                                                value="{{ collect($trackings['return']['objeto']) }}">
                                            <input type="hidden" name="trackingCodeUser"
                                                value="{{ collect($trackingCodeUser) }}">
                                        @endif 
                                    <button type="submit" class="btn btn-success px-3 py-1 px-3 py-1" {{ !empty($trackings) ? '' : 'disabled' }}> <i class="fa fa-download"></i> Download </button> 
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- Report Generato end --}}
                </div>
                {{-- table of kpi --}}
                <div class="row">
                    <table class="table  table-borderless p-0 table-responsive-md table-striped  " id="kpiReportTable">
                        <thead style="backgroud-color:#000" class="">
                            <tr class="">
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
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">No Trackings Found</td>

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
            calculation();
        });

        function calculation() {
            var totalRecords = $('#kpiReportTable tbody').find('tr.count').length;
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
            $('#total').html(totalRecords);
            $('#delivered').html(deliveredOrder );
            $('#taxed').html(taxOrder + ' %');
            $('#returned').html(returnOrder + ' %');
            $('#inProcess').html('Processing or In Transit: ' + inTransit + ' %');
        }
      </script>
@endsection

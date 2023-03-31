@extends('layouts.master')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">
@endsection
@section('page')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <section>
        <div class="row mt-4">
            <div class="col-12 mx-2">
                <div class="">
                    <div class="ml-3">
                        <dl>
                            <dt class=" font-weight-bold dt">Welcome back, {{ Auth::user()->full_name }} 👋</dt>
                            <dd class="display-5 my-3 font-weight-light pb-2 mb-5">Your current kpi report is here</dd>
                        </dl>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div  class="col-lg-8 col-sm-12 d-flex flex-column justify-content-between" >
                                {{-- contanct us banner --}}
                                {{-- <div class="">

                                </div> --}}
                                <div class="">
                                    <div class="row mt-0">
                                        <div class="col-12 pb-xl-2 pb-1 h-25">
                                            <a href="{{ url('tickets') }}"> <img class="banner" src="{{ asset('images/kpi-banner.png') }}" width="100%" height="auto" alt="contact us"> </a>
                                        </div>
                                    </div>
                                </div>
                                {{-- contanct us banner end --}}
                                {{-- orders details cards --}}
                                <div class="row d-flex justify-content-between">
                                    <div class="col-md-3 col-sm-6 custom-cards ">
                                        <div class="card imagecard imagecard-topimage pr-lg-2 mr-lg-2">
                                            <div class="icon-background m-4 p-5 d-flex justify-content-center"  style="background-color: #EEFAFA">
                                                  <img src="{{ asset('app-assets/images/icons/chart.svg') }}" class="icon" width="60" height="60" />
                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <h3 class="text-center font-weight-bold my-1 " id="total">0 </h3>
                                                    <p class="text-center display-5">Total Orders</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6  custom-cards">
                                        <div class="card imagecard imagecard-topimage">

                                            <div class="icon-background m-4 p-5 d-flex justify-content-center"  style="background-color: #fff9eb">
                                                   <img src="{{ asset('app-assets/images/icons/tax.svg') }}" class="icon" width="60" height="60" />  

                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <h3 class="text-center font-weight-bold my-1 " id="taxed">0 </h3>
                                                    <p class="text-center display-5">Taxed</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6  custom-cards"> 
                                        <div class="card imagecard imagecard-topimage"> 
                                            <div class="icon-background m-4 p-5 d-flex justify-content-center"  style="background-color: #eefafa">
                                                 <img src="{{ asset('app-assets/images/icons/delivered.svg') }}"  class="icon" width="60" height="60" />
                                            </div>
                                            <div class="card-body">
                                                <div>                                                    
                                                    <h3 class="text-center font-weight-bold my-1 " id="delivered">0 </h3>
                                                    <p class="text-center display-5">Delivered</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6  custom-cards ">
                                        <div class="card imagecard imagecard-topimage ml-xl-2 pl-xl-2">
                                            <div class="icon-background m-4 p-5 d-flex justify-content-center"  style="background-color: #fbeffb">
                                               <img src="{{ asset('app-assets/images/icons/retured.svg') }}"  class="icon" width="60" height="60" />  
                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <h3 class="text-center font-weight-bold my-1 " id="retured">0 </h3>
                                                    <p class="text-center display-5">Retured</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- orders details cards end --}}

                            </div>
                            {{-- Report Report  --}}
                            <div class=" col-lg-4 col-sm-12 d-flex flex-column justify-content-center " >
                                <div class="filter-card " id="filter-card">
                                    <h4 class="text-center m-4 font-weight-bold">
                                        Report Generator
                                    </h4>
                                    <form action="{{ route('admin.reports.kpi-report.index') }}" method="GET">
                                        <label for="startDate " class="mt-3 mb-2 ">Start Date</label><br>
                                        <div class="input-group">
                                            <input class="form-control py-2 mr-1 p-3" type="date" name="start_date"
                                                id="startDate">
                                        </div>
                                        <label for="end-date" class="mt-4 mb-2"> End Date</label><br>
                                        <div class="input-group">
                                            <input name="end_date" id="endDate" class="form-control py-2 mr-1 p-3"
                                                type="date">
                                        </div>
                                        <label for="tracking_code" class="mt-4 mb-2">Tracking Code</label><br>
                                        <div class="input-group">
                                            <textarea id="tracking_code" value="tracking code" type="text" placeholder="Please Enter Tracking Codes"
                                                rows="4" class="form-control py-2 mr-1" name="trackingNumbers">{{ old('trackingNumbers', request('trackingNumbers')) }}</textarea>
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
                                        <div class="d-flex justify-content-between mt-4 mb-3">
                                            <div>
                                                <button type="submit"
                                                    class="btn btn-outline-success   px-3 py-1">Check
                                                    Details</button>
                                            </div>
                                            <div>
                                    </form>
                                    <form class="row col-12 p-0 m-0"
                                        action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                                        @csrf
                                        @if ($trackings)
                                            <input type="hidden" name="order"
                                                value="{{ collect($trackings['return']['objeto']) }}">
                                            <input type="hidden" name="trackingCodeUser"
                                                value="{{ collect($trackingCodeUser) }}">
                                        @endif
                                        <button type="submit" class="btn btn-success  px-3 py-1 px-3 py-1"
                                            {{ !empty($trackings) ? '' : 'disabled' }}> <i class="fa fa-download"></i>
                                            Download </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- Report Generato end --}}
                </div>
                {{-- table of kpi --}}
                <div class="">
                    <table class="table  table-borderless p-0 table-responsive-md table-striped  " id="kpi-report">
                        <thead style="backgroud-color:#000" class="">
                            <tr class="" id="kpiHead">
                                <th class="py-4">Order Date</th>
                                <th class="py-4">User</th>
                                <th class="py-4">@lang('orders.Tracking')</th>
                                <th class="py-4">@lang('orders.Type Package')</th>
                                <th class="py-4">@lang('orders.First Event')</th>
                                <th class="py-4">@lang('orders.Last Event')</th>
                                <th class="py-4">@lang('orders.Days Between')</th>
                                <th class="py-4">@lang('orders.Last Event')</th>
                                <th class="py-4">@lang('orders.Taxed')</th>
                                <th class="py-4">@lang('orders.Delivered')</th>
                                <th class="py-4">@lang('orders.Returned')</th>
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
                        <tbody>  
                            @if ($trackings)
                                @foreach ($trackings['return']['objeto'] as $data)
                                    @if (isset($data['evento']))
                                        <tr class="count">
                                            @if (optional($data) && isset(optional($data)['numero']))
                                                <td  class="py-4"><p>{{ optional($orderDates[optional($data)['numero']])->order_date }} </p></td>
                                                <td  class="py-4"><p>{{ optional($trackingCodeUser[optional($data)['numero']])->pobox_name }}  </p></td>
                                                <td  class="py-4"><p>{{ optional($data)['numero'] }}</p></td>
                                                <td  class="py-4"><p><span>{{ optional($data)['categoria'] }}</span></p></td>
                                                <td  class="py-4"><p>{{ optional(optional(optional($data)['evento'])[count($data['evento']) - 1])['data'] }}  </p></td>
                                                <td  class="py-4"><p>{{ optional(optional(optional($data)['evento'])[0])['data'] }}  </p></td>
                                                <td  class="py-4"><p>{{ sortTrackingEvents($data, null)['diffDates'] }} </p></td>
                                                <td  class="py-4"><p>{{ optional(optional(optional($data)['evento'])[0])['descricao'] }} </p></td>
                                                <td  class="py-4"><p>{{ sortTrackingEvents($data, null)['taxed'] }}</p></td>
                                                <td  class="py-4"><p>{{ sortTrackingEvents($data, null)['delivered'] }}</p></td>
                                                <td  class="py-4"><p>{{ sortTrackingEvents($data, null)['returned'] }}</p></td>
                                            @else
                                                <td colspan="11">No Trackings Found</td>
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
            $(this).html('<input type="text" class="form-control py-4" placeholder="Search ' + title + '" />');
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
        $('#total').html(totalRecords);
        $('#delivered').html(parseInt(deliveredOrder) + ' %');
        $('#taxed').html(parseInt(taxOrder) + ' %');
        $('#returned').html(returnOrder + ' %');
        $('#inProcess').html('Processing or In Transit: ' + inTransit + ' %');
        document.getElementById("kpiHead").style.backgroundColor = "#eefafa";

    }
</script>
@endsection
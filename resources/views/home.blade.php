@extends('layouts.master')
@section('css')
    <script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.min.js') }}"></script>
    <style>
        .order-card {
            color: #fff;
        }

        .bg-c-blue {
            background: linear-gradient(45deg, #A5DB92, #007C05);
        }

        .bg-c-green {
            background: linear-gradient(45deg, #52B6FE, #6154FE);
        }

        .bg-c-yellow {
            background: linear-gradient(45deg, #FF2C6B, #FF94A1);
        }

        .bg-c-pink {
            background: linear-gradient(45deg, #FF974B, #FF94A1);
        }


        .card_block {
            border-radius: 15px;
            -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            border: none;
            margin-bottom: 25px;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .card_block .card-block {
            padding: 30px;
        }

        .order-card i {
            font-size: 26px;
        }

        .f-left {
            float: left;
        }

        .f-right {
            float: right;
        }
    </style>
@endsection

@section('page')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb light-color">
            <li class="breadcrumb-item"><a href="#"> <img src="{{ asset('images/icon/dashboard.svg') }}"
                        alt="dashboard"></a></li>
            {{-- <li class="breadcrumb-item"><a href="#">Library</a></li> --}}
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    {{-- contact us --}}
    <div class='row no-gutters d-flex align-items-center'>
        <div class="col-xl-4 col-lg-12">
            <div class="light-green-color welcome-admin height-100">
                <div class="ml-3">
                    <dl>
                        <div class="font-weight-bold large-heading-text pt-3 ">Welcome back, {{ Auth::user()->name }} 👋</div>
                        <dd class="font-weight-light pb-2 mb-4">Your current kpi report is here</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-12 light-green-color  border-radius-15 p-2">
            <div class="row mt-0 ">
                <div class="col-12 pb-xl-2 pb-1 h-25">
                    <a href="{{ url('tickets') }}" target="_blank"> <img class="banner border-radius-15" rounded-corners
                            src="{{ asset('images/kpi-banner.png') }}" width="100%" height="auto" alt="contact us">
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        {{-- <x-stat-cards.all-stats/> --}}
        @if (!Auth::user()->isAdmin())
            <div class="row">
                <div class="col-12">
                    <div class="card p-2">
                        <div class="card-header d-flex flex-column align-items-start pb-0">
                            <h2 class="mb-2">
                                @lang('dashboard.your-pobox')
                            </h2>
                            <p class="mb-0">
                                <strong> {{ auth()->user()->name . ' ' . auth()->user()->last_name }} <br>
                                    {!! auth()->user()->pobox_number !!} </strong>
                                <br>
                                {{-- {!! auth()->user()->getPoboxAddress() ?? '' !!} <br> --}}
                            <table>
                                <thead>
                                    <tr>
                                        <th class="pl-0 pr-3">LTL Truck to</th>
                                        <th>Parcels via UPS | FedEx | USPS sent to</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pl-0 pr-3">{!! auth()->user()->getPoboxAddress() ?? '' !!}</td>
                                        <td>
                                            8305 NW 116<sup>th</sup> Avenue<br>
                                            Doral , FL 33178<br>
                                            United States <br>
                                            <span>Ph#: +13058885191</span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- <x-charts.orders-charts/> --}}
        <div class="card border-radius-15 mb-3">
            <livewire:dashboard.stats-filter />
        </div> 
            <div class="row no-gutters">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 card border-radius-15">
                    <div class="mr-lg-3 mr-md-0 ">
                        <h4 class="pt-4 pl-3 font-weight-light">Shipped Orders Analytics</h4>
                        <canvas id="bar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 ">
                    <div class="ml-lg-3 ml-md-0 card border-radius-15">
                        <div>
                            <h4 class="pt-4 pl-3 font-weight-light ">Total Orders</h4>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <h6 class='font-weight-light'>Total Monthly Order</h6>
                                    <h2 class='font-weight-bold'> {{ $orders['currentmonthTotal'] }} </h2>
                                    <div class="d-flex align-items-center">
                                        <img class="mb-2"
                                            src="{{ asset('images/icon/' . ($orders['percentIncreaseThisMonth'] > 0 ? 'increase' : 'decrease') . '.svg') }}">
                                        <h6 class="font-weight-light">
                                            <span
                                                class="{{ $orders['percentIncreaseThisMonth'] > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $orders['percentIncreaseThisMonth'] }} %
                                            </span> month
                                        </h6>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="font-weight-light">Total Year Order</h6>
                                    <h1  class='font-weight-bold'> {{ $orders['currentYearTotal'] }} </h1>
                                    <div class="d-flex  align-items-center">
                                        <img
                                            src="{{ asset('images/icon/' . ($orders['percentIncreaseThisYear'] > 0 ? 'increase' : 'decrease') . '.svg') }}">
                                        <h6 class="font-weight-light">
                                            <span
                                                class="{{ $orders['percentIncreaseThisYear'] > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $orders['percentIncreaseThisYear'] }} % </span>
                                            year
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center" style="height:300px">
                                <canvas id="doughnut"></canvas>

                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

    </section>
    <!-- Dashboard Analytics end -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const bar = document.getElementById('bar');
        const labels = {!! json_encode($orders['months'], JSON_HEX_TAG) !!}
        const totalShippedCount = {!! json_encode($orders['totalShippedCount'], JSON_HEX_TAG) !!}
        const totalOrderCount = {!! json_encode($orders['totalOrderCount'], JSON_HEX_TAG) !!}
        const doughnutData = {!! json_encode($orders['doughnutData'], JSON_HEX_TAG) !!}
        new Chart(bar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Shipped Orders',
                    data: totalShippedCount,
                    borderWidth: 1,
                    backgroundColor: '#1171b2',
                }, {
                    label: 'Total Orders',
                    data: totalOrderCount,
                    borderWidth: 1,
                    backgroundColor: '#1ec09a',
                }, ]
            },
            options: {
                scales: {
                    xAxes: [{
                        offset: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                plugins: {
                    legend: {
                        
                        fullSize: true,
                        align: 'end', 
                        lineWidth: 4, 
                        display: true,
                        labels: {  
                            usePointStyle: true,
                        },
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true, 
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true, 
                        }
                    }
                }
            }
        });

        var ctx = document.getElementById('doughnut');
        var doughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Shipped',
                    'Paid',
                    'Pending',
                    'Released',
                    'Cancelled',
                    'Refunded',
                ],
                datasets: [{
                    label: '#Orders',
                    data: doughnutData,
                    backgroundColor: [
                        'rgb(22, 93, 255)',
                        'rgb(80, 205, 137)',
                        'rgb(255, 199, 0)',
                        'rgb(114, 57, 234)',
                        'rgb(242, 94, 94)',
                        'rgb(181, 189, 203)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: 70,
                plugins: {

                    legend: {
                        fullSize: true,
                        position: 'right',
                        align: 'center', 
                        lineWidth: 4, 
                        display: true,
                        textAlign: 'right',
                        labels: {
                            usePointStyle: true
                        }
                    }
                },
            }
        });
    </script>
@endsection

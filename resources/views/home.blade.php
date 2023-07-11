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
            margin-bottom: 30px;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .card_block .card-block {
            padding: 25px;
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
        <div class="col-4">
            <div class="light-green-color welcome-admin">
                <dl class="mt-2 vertical-center p-2">
                    <dt class="font-weight-bold">Welcome back, {{ Auth::user()->name }} 👋</dt>
                    <dd class="font-weight-light">Your current kpi report is here</dd>
                </dl>
            </div>
        </div>
        <div class="col-8 light-green-color p-2">
            <div class="row mt-0 ">
                <div class="col-12 pb-xl-2 pb-1 h-25">
                    <a href="{{ url('tickets') }}" target="_blank"> <img class="banner rounded-4"
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



        <div class="card  border-radius-15 mt-3">
            <livewire:dashboard.stats-filter />
        </div>
        <div class="d-flex">
            <div class="col-6 card border-radius-15">
                <div class="p-2 mx-1">
                    <canvas id="bar"></canvas>
                </div>
            </div>
            <div class="offset-1  col-5 card border-radius-15">
                <div class="px-5 py-1 mx-1">
                    <h3 class="pt-2 font-weight-bold">Total Orders</h3>
                    <div class="d-flex justify-content-around">
                        <div>
                            <h6 class='font-weight-light'>Total Monthly Order</h6>
                            <h2> {{ $orders['currentmonthTotal'] }} </h2>
                            <div class="d-flex align-items-center">
                                <img class="mb-2" src="{{ asset('images/icon/' . ($orders['percentIncreaseThisMonth'] > 0 ? 'increase' : 'decrease') . '.svg') }}">
                                <h6 class="font-weight-light"> 
                                    <span class="{{ $orders['percentIncreaseThisMonth'] > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $orders['percentIncreaseThisMonth'] }} %
                                    </span> month
                                </h6>
                            </div>
                        </div>
                        <div>
                            <h6 class="font-weight-light">Total Year Order</h6>
                            <h2> {{ $orders['currentYearTotal'] }} </h2>
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
                    borderWidth: 1
                }, {
                    label: 'Total Orders',
                    data: totalOrderCount,
                    borderWidth: 1
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
                        labels: {
                            usePointStyle: true,
                        },
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                }
            }
        });

        var doughnut = document.getElementById('doughnut');
        new Chart(doughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Shipped',
                    'Paid',
                    'Pending',
                    'Released',
                    'Cancelled',
                    'refunded',
                ],
                datasets: [{
                    label: 'My Orders',
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
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                        },
                    }
                },
                cutout: 60,
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                }
            },
        });
    </script>
@endsection

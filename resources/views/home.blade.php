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
        <div class="row mt-3">
            <div class="card col-7 border-radius-15 p-5">
                <canvas id="bar"></canvas>

            </div>
            <div class="card col-5 border-radius-15 p-5">
                <p>Total Orders</p>
                <div class="row">
                    <div class="col-6">
                        <h6>Total Monthly Order</h6> 
                        <h2> {{ $orders['currentmonthTotal'] }} </h2>
                    </div>
                    <div class="col-6"> 
                        <h6>Total Year Order</h6> 
                        <h2> {{ $orders['currentYearTotal'] }} </h2>
                    </div>
                </div>
                <canvas id="doughnut"></canvas> 
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

        new Chart(bar, {
            type: 'bar',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                datasets: [{
                    label: 'Sales Analytics',
                    data: [12, 19, 12, 19, 3, 5, 2, 3, 3, 5, 2, 3],
                    borderWidth: 1
                }, {
                    label: 'Total Orders',
                    data: [12, 1, 3, 19, 3, 5, 2, 3, 3, 5, 2, 3],
                    borderWidth: 1
                }, ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });



        const doughnut = document.getElementById('doughnut');

        new Chart(doughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Red',
                    'Blue',
                    'Yellow'
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [300, 50, 100],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection

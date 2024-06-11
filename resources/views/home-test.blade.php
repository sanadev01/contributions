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



        @media screen and (min-width: 1900px) and (max-width: 3000px) {
            #doughnut {
                width: 480px;
            }

            .card-font-size {
                font-size: 1.4rem;
            }
        }

        @media screen and (min-width: 1700px) and (max-width: 1900px) {
            #doughnut {
                width: 450px;
            }

            .card-font-size {
                font-size: 1.3rem;
            }
        }

        @media screen and (min-width: 1500px) and (max-width: 1700px) {
            #doughnut {
                width: 400px;
            }

            .card-font-size {
                font-size: 1.2rem;
            }
        }

        @media screen and (min-width: 1350px) and (max-width: 1500px) {
            #doughnut {
                width: 380px;
            }

            .card-font-size {
                font-size: 1.1rem;
            }

        }

        @media screen and (min-width: 1200px) and (max-width: 1350px) {
            #doughnut {
                width: 350px;
            }

            .card-font-size {
                font-size: 1rem;
            }

        }

        @media screen and (min-width: 992px) and (max-width: 1300px) {
            #doughnut {
                width: 335px;
            }

            .card-font-size {
                font-size: .9rem;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 992px) {
            #doughnut {
                width: 300px;
            }

            .card-font-size {
                font-size: .8rem;
            }
        }

        @media screen and (min-width: 500px) and (max-width: 768px) {
            .card-font-size {
                font-size: 1.1rem;
            }

            #doughnut {
                width: 350px;
                height: auto;
            }
        }

        @media screen and (min-width: 0px) and (max-width: 500px) {
            .card-font-size {
                font-size: 1rem;
            }

            #doughnut {
                width: auto;
                height: auto;
            }
        }

        @media (max-width: 768px) {
            .sm-wrap-column {
                flex-direction: column;
            }
        }

        #donut {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
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
                        <div class="font-weight-bold large-heading-text pt-3 ">Welcome back, {{ Auth::user()->name }} 👋
                        </div>
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
            <livewire:dashboard.stats-filter-test /> 

        </div>

    </section>
    <!-- Dashboard Analytics end -->
@endsection

@extends('layouts.master')

@section('css')
    <script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.min.js') }}"></script>
    <style>
        .order-card {
            color: #fff;
        }

        .bg-c-blue {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }

        .bg-c-green {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }

        .bg-c-yellow {
            background: linear-gradient(45deg, #FFB64D, #ffcb80);
        }

        .bg-c-pink {
            background: linear-gradient(45deg, #FF5370, #ff869a);
        }


        .card_block {
            border-radius: 5px;
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
                                    <!-- <thead>
                                        <tr>
                                        <th class="pl-0 pr-3">LTL Truck to</th>
                                        <th class="pl-0 pr-3">Parcels via UPS | FedEx | USPS sent to</th>
                                        </tr>
                                    </thead> -->
                                    <tbody>
                                        <tr>
                                            <!-- <td class="pl-0 pr-3">{!! auth()->user()->getPoboxAddress() ?? '' !!}</td> -->
                                            <td class="pl-0 pr-3">
                                                <livewire:dashboard.addresses :user-id="old('user_id')" />
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
        
        <div class="card">
            <livewire:dashboard.stats-filter/>
        </div>
        
    </section>
    <!-- Dashboard Analytics end -->
@endsection

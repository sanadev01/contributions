@extends('layouts.master')

@section('css')
<script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.min.js') }}"></script>
@endsection

@section('page')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        {{-- <x-stat-cards.all-stats/> --}}
        @user
        <div class="row">
            <div class="col-12">
                <div class="card p-2">
                    <div class="card-header d-flex flex-column align-items-start pb-0">
                        <h2 class="mb-2">
                            @lang('dashboard.your-pobox')
                        </h2>
                        <p class="mb-0">
                            <strong> {{ auth()->user()->name.' '.auth()->user()->last_name }} <br> {!! auth()->user()->pobox_number !!} </strong>
                            <br>
                            {{-- {!! auth()->user()->pobox->getCompleteAddress()??'' !!}  <br> --}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @enduser
        {{-- <x-charts.orders-charts/> --}}

        @admin
        {{-- <x-charts.revenue-chart/> --}}
        @endadmin
    </section>
    <!-- Dashboard Analytics end -->
@endsection
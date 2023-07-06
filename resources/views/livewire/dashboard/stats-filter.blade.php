<div>
    <div class="row filter-btn">
        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
            class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                class="feather icon-filter"></i>&nbsp;FILTER</button>
    </div>
    <div class="row col-10">
        <div class=" col-10 text-left mb-2 pl-0" id="dateSearch"
        @if(!empty($startDate) || !empty($endDate)) style="display:block !important" @endif>
            <div class="row my-3 col-8 pl-0">
                <div class="col-md-6">
                    <label for="">@lang('dashboard.Start Date')</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="col-md-6">
                    <label for="">@lang('dashboard.End Date')</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
            </div>
        </div>

        {{-- <div class="custom-control custom-switch custom-control-inline">
            <input id="dateToggle" onclick="toggleDateSearch()" type="checkbox" class="custom-control-input" id="customSwitch1">
            <label class="custom-control-label" for="customSwitch1">
            </label>
            <span class="switch-label">Toggle this switch element</span>
        </div> --}}
        {{-- <div class="custom-control custom-switch custom-control-inline">
            <input id="dateToggle"  type="checkbox" class="custom-control-input" >
            <label class="custom-control-label" for="customSwitch1">
            </label>
            <span class="switch-label">Toggle this switch element</span>
        </div>
        <div class="circleBase type2">
            <div class="fonticon-wrap date-toggle-btn">
                <i class="feather icon-plus-square" onclick="toggleDateSearch()"></i>
            </div>
        </div> --}}

        {{-- <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-green order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">@lang('dashboard.Today Orders')</h5>
                    <h2 class="text-right"><i class="fa fa-cart-plus f-left white"></i><span
                            class="white">{{ $orders['currentDayTotal'] }}</span></h2>
                    <h5 class="m-b-0 white">@lang('dashboard.Completed Orders')<span class="f-right">{{ $orders['currentDayConfirm'] }}</span></h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-yellow order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">@lang('dashboard.Total Month Order',['month'=>$orders['monthName']])</h5>
                    <h2 class="text-right"> <i class="fas fa-calendar-week f-left white"></i> <span
                            class="white">{{ $orders['currentmonthTotal'] }}</span></h2>
                    <h5 class="m-b-0 white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['currentmonthConfirm'] }}</span></h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-pink order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">@lang('dashboard.Current Year')</h5>
                    <h2 class="text-right"><i class="fa fa-credit-card f-left white"></i><span
                            class="white">{{ $orders['currentYearTotal'] }}</span></h2>
                    <h5 class="m-b-0 white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['currentYearConfirm'] }}</span></h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-blue order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">@lang('dashboard.Total Orders')</h5>
                    <h2 class="text-right"><i class="fa fa-cart-plus f-left white"></i><span
                            class="white">{{ $orders['totalOrders'] }}</span></h2>
                    <h5 class="m-b-0 white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['totalCompleteOrders'] }}</span></h5>
                </div>
            </div>
        </div> --}}
    </div>

    {{-- <label class="fonticon-classname mt-1">icon-plus-square</label> --}}
    <div class="row data chartsRow">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row" id="firstCard">
                                <div class="d-flex justify-content-space-around row col-lg-12 smallCharts">
                                    <div class="mt-2">
                                        <h6 class="">@lang('dashboard.Today Orders')</h6>
                                        <h2 class="mb-0 number-font figures">{{ $orders['currentDayTotal'] }}</h2>
                                    </div>
                                    <div class="ms-auto ml-2">
                                        <div class="chart-wrapper mt-1">
                                            <div class="chartjs-size-monitor"
                                                style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div
                                                        style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                    </div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                    </div>
                                                </div>
                                            </div>
                                            <canvas id="cardChart"
                                                class="h-8 w-9 chart-dropshadow chartjs-render-monitor"
                                                style="display: block; width: 64px; height: 64px;">
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted fs-12">
                                <span class="text-primary">
                                    <i class="feather icon-arrow-up text-primary">
                                    </i> 5%
                                </span> Last week</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row" id="secondCard">
                                <div class="row col-lg-12 smallCharts">
                                    <div class="mt-2">
                                        <h6 class="">@lang('dashboard.Month Orders', ['month' => $orders['monthName']])</h6>
                                        <h2 class="mb-0 number-font figures">{{ $orders['currentmonthTotal'] }}</h2>
                                    </div>
                                    <div class="ms-auto ml-2">
                                        <div class="chart-wrapper mt-1">
                                            <div class="chartjs-size-monitor"
                                                style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div
                                                        style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                    </div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                    </div>
                                                </div>
                                            </div>
                                            <canvas id="chart2"
                                                class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64"
                                                height="64" style="display: block; width: 64px; height: 64px;">
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted fs-12">
                                <span id="pink" class="text-pink">
                                    <i class="feather icon-arrow-down text-pink">
                                    </i> 0.75%</span> Last 6 days</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row" id="thirdCard">
                                <div class="row col-lg-12 smallCharts">
                                    <div class="mt-2">
                                        <h6 class="">@lang('dashboard.Current Year',['year'=> date("Y")])</h6>
                                        <h2 class="mb-0 number-font figures">{{ $orders['currentYearTotal'] }}</h2>
                                    </div>
                                    <div class="ms-auto ml-2">
                                        <div class="chart-wrapper mt-1">
                                            <div class="chartjs-size-monitor"
                                                style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div
                                                        style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                    </div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                    </div>
                                                </div>
                                            </div>
                                            <canvas id="chart3"
                                                class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64"
                                                height="64" style="display: block; width: 64px; height: 64px;">
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted fs-12">
                                <span class="text-success">
                                    <i class="feather icon-arrow-up text-success text-green"></i>
                                    0.9%
                                </span> Last 9 days
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row" id="fourthCard">
                                <div class="row col-lg-12 smallCharts">
                                    <div class="mt-2">
                                        <h6 class="">@lang('dashboard.Total Orders')</h6>
                                        <h2 class="mb-0 number-font figures">{{ $orders['totalOrders'] }}</h2>
                                    </div>
                                    <div class="ms-auto ml-5">
                                        <div class="chart-wrapper mt-1">
                                            <div class="chartjs-size-monitor"
                                                style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div
                                                        style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                    </div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink"
                                                    style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div
                                                        style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                    </div>
                                                </div>
                                            </div>
                                            <canvas id="chart4"
                                                class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64"
                                                height="64"
                                                style="display: block; width: 64px; height: 64px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div> <span class="text-muted fs-12"><span class="text-warning"><i
                                        class="feather icon-arrow-up text-success text-warning"></i> 0.6%</span> Last
                                year</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Analytics</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex mx-auto text-center justify-content-center mb-4">
                        <div class="d-flex text-center justify-content-center me-3">
                            <span class="fa fa-circle mt-1 font-small-3 text-primary mr-25"></span>Total Sales
                        </div>
                        <div class="d-flex text-center justify-content-center">
                            <span class="fa fa-circle mt-1 ml-2 font-small-3 text-info mr-25"></span>Total Orders
                        </div>
                    </div>
                    <div class="chartjs-wrapper-demo">
                        {{-- <div class="chartjs-size-monitor"
                            style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                </div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                </div>
                            </div>
                        </div> --}}
                        <canvas id="myChart" class="myChart chartjs-render-monitor"
                            style="display: block; width: 705px;" width="705">
                        </canvas>
                    </div>
                </div>
            </div>
        </div> <!-- COL END -->
        <div class="col-lg-3 col-12 col-xl-3">
            <div class="card crd-height-custom" style="height: 93.1%">
                <div class="card-header card-title m-0 p-2 notification-card-right activityHead"
                    style="justify-content:  center !important">
                    <span class="notification-title activityHeader">Recent Orders</span>
                </div>
                {{-- <div class="card-content"> --}}
                <div class="card-body">
                    <ul class="timeline-left list-unstyled">
                        <li class="">
                            @foreach ($orders['lastFive'] as $order)
                                <div class="media align-items-start border-bottom-10">
                                    <div class="media-body">
                                        <h6 class="primary media-heading">{{ $order->user->name }}</h6><small
                                            class="notification-text"> </small>
                                    </div>
                                    <a href="#" class="mb-1 d-block" data-toggle="modal"
                                        data-target="#hd-modal"
                                        data-url="{{ route('admin.modals.order.invoice', $order) }}">
                                        WHR#: {{ $order->warehouse_number }}
                                    </a>
                                </div>
                                <hr class="custom-margin-hr">
                            @endforeach
                            {{-- <li class="dropdown-menu-footer pt-4"></li> --}}
                        </li>
                    </ul>
                </div>
                <div class="card-footer"> <a class="dropdown-item p-1 text-center"
                        href="{{ route('admin.orders.index') }}">See
                        all orders</a></div>
            </div>
        </div>
    </div>
</div>
@include('layouts.livewire.loading')
</div>

<div>
    <div class="row filter-btn">
        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-filter"></i>&nbsp;Filter</button>
    </div>
    <div class="row col-10">
        <div class=" col-10 text-left mb-2 pl-0" id="dateSearch">
            <div class="row my-3">
                <div class="col-md-4">
                    <label for="">@lang('dashboard.Start Date')</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="col-md-4">
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
    @include('layouts.livewire.loading')
</div>
    <div class="row data chartsRow"> 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12"> 
        <div class="row"> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3"> 
                <div class="card overflow-hidden"> 
                    <div class="card-body"> 
                        <div class="row ml-4">
                            <div class="d-flex justify-content-space-around row smallCharts">
                            <div class="mt-2"> 
                                <h6 class="">@lang('dashboard.Today Orders')</h6> 
                                <h2 class="mb-0 number-font figures">{{ $orders['currentDayTotal'] }}</h2> 
                            </div> 
                            <div class="ms-auto ml-2"> 
                                <div class="chart-wrapper mt-1">
                                    <div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                            </div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                            <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                            </div>
                                        </div>
                                    </div> 
                                    <canvas id="cardChart" class="h-8 w-9 chart-dropshadow chartjs-render-monitor" style="display: block; width: 64px; height: 64px;">
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
                                <div class="row ml-4"> 
                                    <div class="row smallCharts">
                                    <div class="mt-2"> 
                                        <h6 class="">@lang('dashboard.Total Month Order',['month'=>$orders['monthName']])</h6> 
                                        <h2 class="mb-0 number-font figures">{{ $orders['currentmonthTotal'] }}</h2> 
                                    </div> 
                                    <div class="ms-auto ml-2"> 
                                        <div class="chart-wrapper mt-1">
                                            <div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                    </div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                    </div>
                                                </div>
                                            </div> 
                                            <canvas id="chart2" class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64" height="64" style="display: block; width: 64px; height: 64px;">
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
                                                <div class="row ml-4"> 
                                                <div class="row smallCharts">
                                                    <div class="mt-2"> 
                                                        <h6 class="">@lang('dashboard.Current Year')</h6> 
                                                        <h2 class="mb-0 number-font figures">{{ $orders['currentYearTotal'] }}</h2> 
                                                    </div> 
                                                    <div class="ms-auto ml-2"> 
                                                        <div class="chart-wrapper mt-1">
                                                            <div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                                    </div>
                                                                </div>
                                                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <canvas id="chart3" class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64" height="64" style="display: block; width: 64px; height: 64px;">
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
                                                <div class="row ml-4">
                                                <div class="row smallCharts">
                                                    <div class="mt-2"> 
                                                        <h6 class="">@lang('dashboard.Total Orders')</h6>
                                                         <h2 class="mb-0 number-font figures">{{ $orders['totalOrders'] }}</h2> 
                                                    </div> 
                                                        <div class="ms-auto ml-5"> 
                                                            <div class="chart-wrapper mt-1">
                                                                <div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                                                    <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                                                        </div>
                                                                    </div>
                                                                    <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                                <canvas id="chart4" class="h-8 w-9 chart-dropshadow chartjs-render-monitor" width="64" height="64" style="display: block; width: 64px; height: 64px;"></canvas> 
                                                            </div> 
                                                        </div> 
                                                </div>
                                                        </div> <span class="text-muted fs-12"><span class="text-warning"><i class="feather icon-arrow-up text-success text-warning"></i> 0.6%</span> Last year</span> </div> </div> </div> </div> </div> </div>
<div class="row"> 
    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-9"> 
        <div class="card"> 
            <div class="card-header"> 
                <h3 class="card-title">Sales Analytics</h3> 
            </div> 
            <div class="card-body"> 
                <div class="d-flex mx-auto text-center justify-content-center mb-4"> 
                    <div class="d-flex text-center justify-content-center me-3">
                        <span class="fa fa-circle font-small-3 text-primary mr-25">
                            </span>Total Sales
                        </div> 
                        <div class="d-flex text-center justify-content-center">
                            <span class="fa fa-circle font-small-3 text-info mr-25">
                                </span>Total Orders</div> 
                            </div> 
                            <div class="chartjs-wrapper-demo">
                                <div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0">
                                        </div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                                        </div>
                                    </div>
                                </div> 
                                <canvas id="myChart" class="myChart chartjs-render-monitor" height="330" style="display: block; width: 705px; height: 330px;" width="705">
                                </canvas> 
                            </div> 
                        </div> 
                    </div> 
                </div> <!-- COL END --> 
                <div class="col-lg-3 col-12">
                    <div class="card activityCard">
                        <div class="card-title m-0 p-2 notification-card-right">
                            <span class="notification-title">Notifications</span>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="timeline-left list-unstyled">
                                        <li class=""><a class=" justify-content-between">
                                                <div class="media  align-items-start">
                                                    <div class="media-body">
                                                        <h6 class="primary media-heading">You have new order!</h6><small class="notification-text"> Are your going to meet me tonight?</small>
                                                    </div><small>
                                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>
                                                </div>
                                            </a><a class="justify-content-between" href="javascript:void(0)">
                                                <div class="media align-items-start">
                                                    <div class="media-body">
                                                        <h6 class="success media-heading red darken-1">99% Server load</h6><small class="notification-text">You got new order of goods.</small>
                                                    </div><small>
                                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">5 hour ago</time></small>
                                                </div>
                                            </a><a class=" justify-content-between" href="javascript:void(0)">
                                                <div class="media  align-items-start">
                                                    <div class="media-body">
                                                        <h6 class="danger media-heading yellow darken-3">Warning notifixation</h6><small class="notification-text">Server have 99% CPU usage.</small>
                                                    </div><small>
                                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Today</time></small>
                                                </div>
                                            </a><a class=" justify-content-between" href="javascript:void(0)">
                                                <div class="media  align-items-start">
                                                    <div class="media-body">
                                                        <h6 class="info media-heading">Complete the task</h6><small class="notification-text">Cake sesame snaps cupcake</small>
                                                    </div><small>
                                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last week</time></small>
                                                </div>
                                            </a><a class=" justify-content-between" href="javascript:void(0)">
                                                <div class="media  align-items-start">
                                                    <div class="media-body">
                                                        <h6 class="warning media-heading">Generate monthly report</h6><small class="notification-text">Chocolate cake oat cake tiramisu marzipan</small>
                                                    </div><small>
                                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last month</time></small>
                                                </div>
                                        <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="javascript:void(0)">Read all notifications</a></li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

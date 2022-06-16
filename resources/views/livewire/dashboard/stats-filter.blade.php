<div>
    <div class="row col-12">
        <div class=" col-11 text-left mb-2 pl-0">
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

    @include('layouts.livewire.loading')
</div>
    <div class="row data"> 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12"> 
        <div class="row"> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3"> 
                <div class="card overflow-hidden"> 
                    <div class="card-body"> 
                        <div class="">
                            <div class="d-flex justify-content-space-around row smallCharts">
                            <div class="mt-2"> 
                                <h6 class="">Total Users</h6> 
                                <h2 class="mb-0 number-font figures">44,278</h2> 
                            </div> 
                            <div class="ms-auto"> 
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
                                <div class=""> 
                                    <div class="row smallCharts">
                                    <div class="mt-2"> 
                                        <h6 class="">Total Profit</h6> 
                                        <h2 class="mb-0 number-font figures">67,987</h2> 
                                    </div> 
                                    <div class="ms-auto"> 
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
                                                <div class=""> 
                                                <div class="row smallCharts">
                                                    <div class="mt-2"> 
                                                        <h6 class="">Total Expenses</h6> 
                                                        <h2 class="mb-0 number-font figures">$76,965</h2> 
                                                    </div> 
                                                    <div class="ms-auto"> 
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
                                                <div class="">
                                                <div class="row smallCharts">
                                                    <div class="mt-2"> 
                                                        <h6 class="">Total Cost</h6>
                                                         <h2 class="mb-0 number-font figures">$59,765</h2> 
                                                        </div> 
                                                        <div class="ms-auto"> 
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
                        <div class="card-header">
                            <h4 class="card-title">Activity Timeline</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="activity-timeline timeline-left list-unstyled">
                                    <li>
                                        <div class="timeline-icon bg-primary">
                                            <i class="feather icon-plus font-medium-2 align-middle"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold mb-0">Client Meeting</p>
                                            <span class="font-small-3">Bonbon macaroon jelly beans gummi bears jelly lollipop apple</span>
                                        </div>
                                        <small class="text-muted">25 mins ago</small>
                                    </li>
                                    <li>
                                        <div class="timeline-icon bg-warning">
                                            <i class="feather icon-alert-circle font-medium-2 align-middle"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold mb-0">Email Newsletter</p>
                                            <span class="font-small-3">Cupcake gummi bears soufflé caramels candy</span>
                                        </div>
                                        <small class="text-muted">15 days ago</small>
                                    </li>
                                    <li>
                                        <div class="timeline-icon bg-danger">
                                            <i class="feather icon-check font-medium-2 align-middle"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold mb-0">Plan Webinar</p>
                                            <span class="font-small-3">Candy ice cream cake. Halvah gummi bears</span>
                                        </div>
                                        <small class="text-muted">20 days ago</small>
                                    </li>
                                    <li>
                                        <div class="timeline-icon bg-success">
                                            <i class="feather icon-check font-medium-2 align-middle"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold mb-0">Launch Website</p>
                                            <span class="font-small-3">Candy ice cream cake. </span>
                                        </div>
                                        <small class="text-muted">25 days ago</small>
                                    </li>
                                    <li>
                                        <div class="timeline-icon bg-primary">
                                            <i class="feather icon-check font-medium-2 align-middle"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold mb-0">Marketing</p>
                                            <span class="font-small-3">Candy ice cream. Halvah bears Cupcake gummi bears.</span>
                                        </div>
                                        <small class="text-muted">28 days ago</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
<script>
var ctx = document.getElementById('myChart').getContext("2d");;
var gradient = ctx.createLinearGradient(0, 0, 0, 400)
    gradient.addColorStop(0, '#978efc')
    gradient.addColorStop(1, '#dedbfb')
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [
            {
            label: '# of Votes',
            data: [9, 13, 7, 8, 19, 11],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 4,
            fill: false,
            borderColor: '#05c3fb',
        
        },    
        {
            label: '# of Votes',
            data: [12, 19, 12, 18, 10, 11],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 4,
            fill: true,
            borderColor: '#6c5ffc',
            backgroundColor : gradient,
        }
    ]
    },
    options: {
        plugins: {
            legend: {
                display: false
            },
        },
        tension : 0.2,
        scales: {
            x: {
                display: false,
            }
        }
    }
});
var card1 = document.getElementById('cardChart').getContext("2d");;

var cardChart = new Chart(card1, {
    type: 'bar',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [
        {
            data: [12, 19, 12, 18, 10, 11],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1,
            fill: true,
            borderColor: '#6cdafa',
            backgroundColor : '#caf0fb',
            borderRadius: 5,
        }
    ]
    },
    options: {
        tension : 0.2,
        offset : false,
        scales: {
            y: {
                beginAtZero: true,
                display: false
            },
            x: {
                display: false
            }
        },
        plugins: {
            legend: {
                display: false
            },
        }
    }
    
});

var card2 = document.getElementById('chart2').getContext("2d");

var chart2 = new Chart(card2, {
    type: 'line',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [
        {
            data: [12, 19, 12, 8, 2, 6],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 3,
            fill: false,
            borderColor: '#f46ef4',
            borderRadius: 5,
        }
    ]
    },
    options: {
        tension : 0.2,
        offset : false,
        scales: {
            y: {
                beginAtZero: true,
                display: false
            },
            x: {
                display: false
            }
        },
        plugins: {
            legend: {
                display: false
            },
        },
        elements: {
                    point:{
                        radius: 0
                    }
                }
    }
    
});


var card3 = document.getElementById('chart3').getContext("2d");

var chart3 = new Chart(card3, {
    type: 'bar',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [
        {
            data: [12, 19, 12, 8, 2, 6],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 3,
            fill: true,
            borderColor: '#4ecc48',
        }
    ]
    },
    options: {
        tension : 0.2,
        offset : false,
        scales: {
            y: {
                beginAtZero: true,
                display: false
            },
            x: {
                display: false
            }
        },
        plugins: {
            legend: {
                display: false
            },
        },
        elements: {
                    point:{
                        radius: 0
                    }
                }
    }
    
});

var card4 = document.getElementById('chart4').getContext("2d");

var chart4 = new Chart(card4, {
    type: 'line',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [
        {
            data: [12, 19, 12, 8, 2, 6],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 3,
            fill: false,
            borderColor: '#f7ba48',
        }
    ]
    },
    options: {
        tension : 0.2,
        offset : false,
        scales: {
            y: {
                beginAtZero: true,
                display: false
            },
            x: {
                display: false
            }
        },
        plugins: {
            legend: {
                display: false
            },
        },
        elements: {
                    point:{
                        radius: 0
                    }
                }
    }
    
});
</script>
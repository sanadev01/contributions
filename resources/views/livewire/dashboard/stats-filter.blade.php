<div>
<div class="card border-radius-15 mb-4">
<div class="d-flex justify-content-center">

    <div class="row col-12 mt-4 no-gutters">
        <div class=" col-11 text-left mb-5">
            <div class="row my-3">
                <div class="col-md-4 mb-3">
                    <label for="">@lang('dashboard.Start Date')</label>
                    <input type="date" class="form-control h-75 border-radius-10" wire:model="startDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">@lang('dashboard.End Date')</label>
                    <input type="date" class="form-control h-75 border-radius-10" wire:model="endDate">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-green order-card  border-radius-15 mx-1">
                <div class="card-block border-radius-15 pb-2">
                    <div class="row">
                        <div class="col-9">
                            <h6 class="white height-30">@lang('dashboard.Today Orders')</h6>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <img src="{{ asset('images/icon/tickmark.svg') }}">
                            
                        </div>
                    </div>
                    <h2 class="pb-4">
                        <span class="white">{{ $orders['currentDayTotal'] }}</span>
                    </h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right">{{ $orders['currentDayConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-yellow order-card border-radius-15 mx-1">
                <div class="card-block border-radius-15 pb-2">
                    <div class="row">
                        <div class="col-9">
                            <h6 class="white height-30">@lang('dashboard.Total Month Order', ['month' => $orders['monthName']])</h6>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <img src="{{ asset('images/icon/tickmark.svg') }}">
                        </div>
                    </div>
                    <h2 class="pb-4"><span class="white">{{ $orders['currentMonthTotal'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right white">{{ $orders['currentMonthConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 ">
            <div class="card_block bg-c-pink order-card  border-radius-15 mx-1">
                <div class="card-block border-radius-15 pb-2">
                    <div class="row">
                        <h6 class="col-9 white height-30">@lang('dashboard.Current Year')</h6>
                        <div class="col-3 d-flex justify-content-end">
                            <img src="{{ asset('images/icon/tickmark.svg') }}">
                        </div>
                    </div>
                    <h2 class="pb-4"><span class="white"> {{ $orders['currentYearTotal'] }} </span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['currentYearConfirm'] }}</span></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-blue order-card border-radius-15 mx-1">
                <div class="card-block border-radius-15 pb-2">
                    <div class="row">
                        <h6 class="col-9  white height-30">@lang('dashboard.Total Orders')</h6>
                        <div class="col-3 d-flex justify-content-end">
                            <img src="{{ asset('images/icon/tickmark.svg') }}">
                        </div>
                    </div>
                    <h2 class="pb-4"><span class="white">{{ $orders['totalOrders'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')<span class="f-right white">{{ $orders['totalCompleteOrders'] }}</span></h6>
                </div>
            </div>
        </div>  
    </div>
    @include('layouts.livewire.loading')
</div>

</div>
<div class="d-flex sm-wrap-column">
    <div class="p-2  flex-grow-1 card border-radius-15">
        <div class="mr-lg-3 mr-md-0 ">
            <h4 class="pt-lg-4 pt-md-3 pt-sm-1 pl-3 font-weight-light card-font-size">Shipped Orders Analytics</h4>
            <canvas id="bar"></canvas>
        </div>
    </div>
    <div class="ml-md-4 ml-sm-0 p-2 card border-radius-15 " id="doughnutCard">
        <h4 class="pt-lg-4 pt-md-3 pt-sm-1 pl-3 font-weight-light card-font-size">Total Orders</h4>
        <div class="d-flex my-xl-2 my-lg-2 justify-content-around">
            <div class="mx-xl-5 mx-lg-2">
                <h6 class='font-weight-light  '>Total Monthly Order</h6>
                <h2 class='font-weight-bold  md-font-size'> {{ $orders['currentMonthTotal'] }} </h2>
                <div class="d-flex mt-xl-3 mt-lg-1 align-items-center">
                    <img class="mb-lg-2 mb-sm-1"
                        src="{{ asset('images/icon/' . ($orders['percentIncreaseThisMonth'] > 0 ? 'increase' : 'decrease') . '.svg') }}">
                    <h6 class="font-weight-light card-font-size">
                        <span
                            class=" {{ $orders['percentIncreaseThisMonth'] > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $orders['percentIncreaseThisMonth'] }} %
                        </span> month
                    </h6>
                </div>
            </div>
            <div class="mx-xl-5 mx-lg-2">
                <h6 class="font-weight-light  ">Total Year Order</h6>
                <h1 class='font-weight-bold md-font-size'> {{ $orders['currentYearTotal'] }} </h1>
                <div class="d-flex mt-xl-3 mt-lg-1 align-items-center">
                    <img
                        src="{{ asset('images/icon/' . ($orders['percentIncreaseThisYear'] > 0 ? 'increase' : 'decrease') . '.svg') }}">
                    <h6 class="font-weight-light card-font-size">
                        <span
                            class="{{ $orders['percentIncreaseThisYear'] > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $orders['percentIncreaseThisYear'] }} % </span>
                        year
                    </h6>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center h-75">
            <div id="donut"></div>
        </div>
    </div>
</div>
</div>

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.1/js/anychart-base.min.js"></script>

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
        //donut
        anychart.onDocumentReady(function() {
            var chart = anychart.pie(doughnutData);
            chart.innerRadius("75%");
            var label = anychart.standalones.label();
            label.text("Total :" + doughnutData.reduce((a, b) => a + b.value, 0));
            label.width("100%");
            label.height("100%");
            label.adjustFontSize(false);
            label.fontColor("#60727b");
            label.hAlign("center");
            label.vAlign("middle");
            // set the label as the center content
            chart.center().content(label);
            chart.title("");
            chart.container("donut");            
            // chart.legend().unlisten("click", function(){});
            // chart.unlisten("click",function(){}); 
            chart.draw();          
             // remove all listeners
               
        });
    </script>
@endsection

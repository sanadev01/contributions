<div>

    <div class="row col-12 mt-4">
        <div class=" col-11 text-left mb-2">
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
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-green order-card  border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row"> 
                        <div class="col-9"> 
                            <h6 class="white height-30">@lang('dashboard.Today Orders')</h6> 
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <i class="fa fa-cart-plus f-left white"></i>
                        </div>
                    </div>
                    <h2>
                        <span class="white">{{ $orders['currentDayTotal'] }}</span>
                    </h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right">{{ $orders['currentDayConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-yellow order-card border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row"> 
                        <div class="col-9"> 
                            <h6 class="white height-30">@lang('dashboard.Total Month Order', ['month' => $orders['monthName']])</h6>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <i class="fas fa-calendar-week f-left white"></i>
                        </div>
                    </div>
                    <h2><span class="white">{{ $orders['currentmonthTotal'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right white">{{ $orders['currentmonthConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-pink order-card  border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row">
                        <h6 class="col-9 white height-30">@lang('dashboard.Current Year')</h6>
                        <div class="col-3 d-flex justify-content-end">
                            <i class="fa fa-credit-card f-left white"></i> 
                        </div>
                    </div>
                    <h2><span class="white"> {{ $orders['currentYearTotal'] }} </span></h2>  
                    <h6 class="white">@lang('dashboard.Completed Orders')<span  class="f-right white">{{ $orders['currentYearConfirm'] }}</span></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-blue order-card  border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row">
                         <h6 class="col-9  white height-30">@lang('dashboard.Total Orders')</h6>
                         <div class="col-3 d-flex justify-content-end">
                                 <i class=" fa fa-cart-plus f-left white"></i>
                         </div>
                    </div>
                    <h2><span class="white">{{ $orders['totalOrders'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')<span class="f-right white">{{ $orders['totalCompleteOrders'] }}</span></h6>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

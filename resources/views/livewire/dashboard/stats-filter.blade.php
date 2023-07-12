<div class="d-flex justify-content-center">

    <div class="row col-12 mt-4 no-gutters">
        <div class=" col-11 text-left mb-5">
            <div class="row my-3">
                <div class="col-md-4 mb-3">
                    <label for="">@lang('dashboard.Start Date')</label>
                    <input type="date" class="form-control form-control h-75 border-radius-10" wire:model="startDate">
                </div>
                <div class="col-md-4">
                    <label for="">@lang('dashboard.End Date')</label>
                    <input type="date" placeholder="dd/mm/yy" class="form-control form-control h-75 border-radius-10" wire:model="endDate">
                </div>
            </div>
        </div>
        {{-- <div class="row d-flex justify-content-center col-12"> --}}
  
 
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
                    <h2 class="pb-4"><span class="white">{{ $orders['currentmonthTotal'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right white">{{ $orders['currentmonthConfirm'] }}</span>
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

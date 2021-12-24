<div>
    <div class="row col-12 mt-4">
        <div class=" col-11 text-left mb-2">
            <div class="row my-3">
                <div class="col-md-4">
                    <label for="">Start Date</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="col-md-4">
                    <label for="">End Date</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card_block bg-c-green order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">@lang('dashboard.today-orders')</h5>
                    <h2 class="text-right"><i class="fa fa-cart-plus f-left white"></i><span
                            class="white">{{ $orders['currentDayTotal'] }}</span></h2>
                    <p class="m-b-0">@lang('dashboard.total-completed-order')<span class="f-right">{{ $orders['currentDayConfirm'] }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card_block bg-c-yellow order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">Total {{ $orders['monthName'] }} Orders</h5>
                    <h2 class="text-right"> <i class="fas fa-calendar-week f-left white"></i> <span
                            class="white">{{ $orders['currentmonthTotal'] }}</span></h2>
                    <p class="m-b-0">Completed Orders<span
                            class="f-right white">{{ $orders['currentmonthConfirm'] }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card_block bg-c-pink order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">Total Current Year Orders</h5>
                    <h2 class="text-right"><i class="fa fa-credit-card f-left white"></i><span
                            class="white">{{ $orders['currentYearTotal'] }}</span></h2>
                    <p class="m-b-0">Total Completed Orders<span
                            class="f-right white">{{ $orders['currentYearConfirm'] }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card_block bg-c-blue order-card">
                <div class="card-block">
                    <h5 class="m-b-20 white">Total Orders</h5>
                    <h2 class="text-right"><i class="fa fa-cart-plus f-left white"></i><span
                            class="white">{{ $orders['totalOrders'] }}</span></h2>
                    <p class="m-b-0">Today Completed Orders<span
                            class="f-right white">{{ $orders['totalCompleteOrders'] }}</span></p>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

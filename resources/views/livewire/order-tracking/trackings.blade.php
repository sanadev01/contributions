<div>
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <input type="text" placeholder="Enter Tracking Number" class="form-control offset-3 col-9 w-100 text-center border border-primary" style="height: 50px; font-size: 30px;" wire:model.debounce.500ms="trackingNumber">
        </div>
        <div class="col-md-4 col-sm-4">
            <button class="btn btn-primary btn-lg" wire:click="trackOrder">Search</button>
        </div>
    </div>
    @if ($tracking)
    {{ dd($tracking) }}
        <hr>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Tracking Package
                        </h4>
                        <div>
                            <a href="{{ route('tracking.index') }}" class="btn btn-primary"> <i class="fa fa-search"></i> Tracking Package </a>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="row col-12 d-flex justify-content-between px-3 top">
                            <div class="col-3 d-flex">
                                <h6>HD ORDER WHR#: <span class="text-primary font-weight-bold">{{ $tracking->order->warehouse_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ $tracking->order->corrios_tracking_code }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Dispatch Number: <span class="text-primary font-weight-bold">{{ optional(optional($tracking->order->containers)[0])->dispatch_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>AWB#: <span class="text-primary font-weight-bold">{{ optional(optional($tracking->order->containers)[0])->awb }}</span></h6>
                            </div>
                            
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12">
                                <ul id="progressbar" class="text-center">
                                    <li class="@if($tracking->order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li>
                                    <li class="@if($tracking->order->status >= App\Models\Order::STATUS_PAYMENT_DONE ) active @endif step0"></li>
                                    <li class="@if($tracking->order->status >= App\Models\Order::STATUS_SHIPPED ) active @endif step0"></li>
                                    {{-- <li class="@if($tracking->order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li>
                                    <li class="@if($tracking->order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="row justify-content-between top">
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/order.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Processed</p>
                                </div>
                            </div>
                            {{-- <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/GiWFtVu.png">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Designing</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/u1AzR7w.png">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Shipped</p>
                                </div>
                            </div> --}}
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/onway.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>En Route</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/shipped.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Shipped</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


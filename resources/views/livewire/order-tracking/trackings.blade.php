<div>
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <input type="text" placeholder="Enter Tracking Number" class="form-control offset-3 col-9 w-100 text-center border border-primary" style="height: 50px; font-size: 30px;" wire:model.debounce.500ms="trackingNumber">
        </div>
        <div class="col-md-4 col-sm-4">
            <button class="btn btn-primary btn-lg" wire:click="trackOrder">Search</button>
        </div>
    </div>
   
    {{-- 
        @if($tracking->order->status >= App\Models\Order::STATUS_PAYMENT_DONE ) active @endif
        @if($tracking->order->status >= App\Models\Order::STATUS_SHIPPED ) active @endif --}}
        @if ($tracking)
        
        <hr>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Find Your Package
                        </h4>
                        
                    </div>
                    
                    {{-- <div class="card"> --}}
                        <div class="row col-12 d-flex justify-content-between px-3 top">
                            <div class="col-3 d-flex">
                                <h6>HD WHR#: <span class="text-primary font-weight-bold">{{ optional($tracking->order)->warehouse_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ optional($tracking->order)->corrios_tracking_code }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Dispatch Number: <span class="text-primary font-weight-bold">{{ optional(optional($tracking->order->containers)[0])->dispatch_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>AWB#: <span class="text-primary font-weight-bold">{{ optional(optional($tracking->order->containers)[0])->awb }}</span></h6>
                            </div>
                            
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12">
                                <ul id="progressbar" class="text-center">
                                    @if ($tracking->type == 'HD')
                                        <li class="@if($tracking->status_code >=  70) active @endif step0"></li>
                                        <li class="@if($tracking->status_code >=  73) active @endif step0"></li>
                                        <li class="@if($tracking->status_code >=  75) active @endif step0"></li>
                                        <li class="@if($tracking->status_code >=  80) active @endif step0"></li>
                                        <li class="step0"></li>
                                        <li class="step0"></li>
                                    @else
                                        <li class="active step0"></li>
                                        <li class="active step0"></li>
                                        <li class="active step0"></li>
                                        <li class="active step0"></li>
                                        <li class="@if($tracking->status_code =  01) active @endif step0"></li>
                                        <li class="@if($tracking->status_code =  01) active @endif step0"></li>
                                    @endif
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="row justify-content-between top">
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/to-hd.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Freight  come <br> from customer </p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/hd-whr.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Warehouse <br> Receive</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/container.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Parcels Scan in <br> the Container </p>
                                </div>
                            </div>
                            
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/to-air.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Container <br> goto Airport </p>
                                </div>
                            </div>
                            
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/awb.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Airplane <br> Departure MIA</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/correios.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Arrival in <br> Destination</p>
                                </div>
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    @endif
</div>


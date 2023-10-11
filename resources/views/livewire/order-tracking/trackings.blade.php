<div>

    {{-- @if ($message)
        <div class="content-header row col-8 offset-2">
            <div class="col-md-12">
                <div class="@if ($status == 201) alert alert-info @endif @if($status == 404) alert alert-warning @endif no-print">
                    <h4>@if ($status == 201) info @endif @if($status == 404) Warning @endif !</h4>
                    <p>{{ $message }}</p>
                </div>
            </div>
        </div>
    @endif --}}
    <div class="row mb-5">
        <div class="col-md-8 col-sm-8">
            <input type="text" placeholder="Enter Tracking Number" class="form-control offset-4 col-6 w-100 text-center border border-primary" style="height: 35px; font-size: 20px;" wire:model.defer="trackingNumber">
        </div>
        <div class="col-md-2 col-sm-2">
            <button class="btn btn-primary btn-lg" wire:click="trackOrder">Search</button>
        </div>
        @if ($trackings)
            <div class="col-md-2 col-sm-2">
                <button wire:click="download" class="btn btn-success btn-lg" wire:click="trackOrder">Download</button>
            </div>
        @endif
    </div>

    @if ($trackings)
        <div id="accordion">
            @foreach ($trackings as $tracking)

                @if(optional($tracking)['success'] && optional($tracking)['status'] == 200)
                <div class="card">
                    <div class="card-header pt-2" id="t-{{optional($tracking['order'])->warehouse_number}}">
                    <h5 class="col-12">
                        <button class="col-12 btn btn-link collapsed" data-toggle="collapse" data-target="#t-{{optional($tracking['order'])->corrios_tracking_code}}" aria-expanded="false" aria-controls="t-{{optional($tracking['order'])->corrios_tracking_code}}">
                            <div class="row col-12 d-flex justify-content-between">
                                <div class="col-3 d-flex">
                                    <h6>HD WHR#: <span class="text-primary font-weight-bold">{{ optional($tracking['order'])->warehouse_number }}</span></h6>
                                </div>
                                <div class="col-3 d-flex">
                                    <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ optional($tracking['order'])->corrios_tracking_code }}</span></h6>
                                </div>
                                <div class="col-3 d-flex">
                                    <h6>Piece: <span class="text-primary font-weight-bold">{{ optional($tracking['order'])->items->count() }}</span></h6>
                                </div>
                                <div class="col-3 d-flex">
                                    <h6>weight: <span class="text-primary font-weight-bold">{{ optional($tracking['order'])->weight }} {{ optional($tracking['order'])->measurement_unit }}</span></h6>
                                </div>
                            </div>
                        </button>
                    </h5>
                    </div>
                    <div id="t-{{optional($tracking['order'])->corrios_tracking_code}}" class="collapse" aria-labelledby="t-{{optional($tracking['order'])->warehouse_number}}" data-parent="#accordion">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-12">
                                            <ul id="progressbar" class="text-center d-flex justify-content-center">
                                                @if ($tracking['service'] == 'HD')
                                                    <li class="@if($tracking['trackings']->last()->status_code >=  70) active @endif step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/order-placed.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Order<br>Placed </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="@if($tracking['trackings']->last()->status_code >=  72) active @endif step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Freight<br>in transit </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="@if($tracking['trackings']->last()->status_code >=  73) active @endif step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/hd-whr.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Received<br>Terminal </p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="@if($tracking['trackings']->last()->status_code >=  75) active @endif step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/container.png') }}">
                                                            <div class="d-flex flex-column mt-4">
                                                                <p class="font-weight-bold">Processed/ <br> manifested </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="@if($tracking['trackings']->last()->status_code >=  80) active @endif step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/awb.png') }}">
                                                            <div class="d-flex flex-column mt-4">
                                                                <p class="font-weight-bold">@if($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)Posted @else Shipped @endif</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            @if ($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)
                                                                <img class="icon offset-1" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                            @elseif($tracking['order']->recipient->country_id == \App\Models\Order::CHILE)
                                                                <img class="icon offset-1" src="{{ asset('images/tracking/chile-flag.png') }}">
                                                            @else
                                                                <img class="icon offset-1" src="{{ asset('images/tracking/ups-logo.png') }}">
                                                            @endif
                                                            <div class="d-flex flex-column" mt-4>
                                                                @if ($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)
                                                                    <p class="font-weight-bold">Received <br>by Correios</p>
                                                                @elseif ($tracking['order']->recipient->country_id == \App\Models\Order::CHILE)
                                                                    <p class="font-weight-bold">Received <br>Correios Chile</p>
                                                                @else
                                                                    <p class="font-weight-bold">Received <br>by UPS</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @if ($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">Customs clearance<br>finalized</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif
                                                    @if($tracking['order']->recipient->country_id != \App\Models\Order::US)
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">In transit <br>to @if ($tracking['order']->recipient->country_id == \App\Models\Order::CHILE) {{ optional(optional(optional($tracking['order'])->recipient)->state)->name }} @endif {{ optional(optional($tracking['order'])->recipient)->city }}</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif

                                                    @if ($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">Out for<br>delivery </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif
                                                    @if($tracking['order']->recipient->country_id == \App\Models\Order::US)
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">Arrived at <br> UPS Facility</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">Departed from <br> UPS Facility</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif
                                                    <li class="step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1" src="{{ asset('images/tracking/delivered.png') }}">
                                                            <div class="d-flex flex-column" mt-4>
                                                                <p class="font-weight-bold">parcels delivered</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @else
                                                    <li class="active step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/order-placed.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Order<br>Placed </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="active step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Freight<br>in transit </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="active step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/hd-whr.png') }}">
                                                            <div class="d-flex flex-column mt-2">
                                                                <p class="font-weight-bold">Received<br>Terminal </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="active step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/container.png') }}">
                                                            <div class="d-flex flex-column mt-4">
                                                                <p class="font-weight-bold">Processed/ <br> manifested </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="active step0">
                                                        <div class="icon-content">
                                                            <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/awb.png') }}">
                                                            <div class="d-flex flex-column mt-4">
                                                                <p class="font-weight-bold">@if($tracking['order']->recipient->country_id == \App\Models\Order::BRAZIL)Posted @else Shipped @endif</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @if ($tracking['service'] == 'Correios_Brazil')
                                                        <li class="@if( $this->toggleBrazilStatus($tracking['api_trackings'], $tracking['trackings']) >= 90) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Received <br>by Correios</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleBrazilStatus($tracking['api_trackings'], $tracking['trackings']) >= 100) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Customs<br>clearance finalized</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleBrazilStatus($tracking['api_trackings'], $tracking['trackings']) >= 110) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">In transit <br>to @if ($tracking['order']->recipient->country_id == \App\Models\Order::CHILE) {{ optional(optional($tracking['order']->recipient)->state)->name }} @endif {{ optional($tracking['order']->recipient)->city }}</p>
                                                                </div>
                                                            </div>
                                                        </li>

                                                        <li class="@if( $this->toggleBrazilStatus($tracking['api_trackings'], $tracking['trackings']) >= 120) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Out for  <br> delivery</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleBrazilStatus($tracking['api_trackings'], $tracking['trackings']) >= 130) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/delivered.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">parcels delivered </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @elseif( $tracking['service'] == 'Correios_Chile' )
                                                        <li class="@if( $this->toggleChileStatus($tracking['api_trackings']) >= 90) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/chile-flag.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Received By<br>Correios Chile </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleChileStatus($tracking['api_trackings']) >= 100) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcels in <br> transit to @if(isset($tracking['Oficina'])) in {{ $tracking['Oficina'] }} @endif</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleChileStatus($tracking['api_trackings']) >= 110) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcels delivered</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @elseif( $tracking['service'] == 'UPS' )
                                                        <li class="@if( $this->toggleUpsStatus($tracking['api_trackings']) >= 90 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/ups-logo.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Received <br>by UPS</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleUpsStatus($tracking['api_trackings']) >= 100 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Arrived at <br> UPS Facility</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleUpsStatus($tracking['api_trackings']) >= 110 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Departed from <br> UPS Facility</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleUpsStatus($tracking['api_trackings']) >= 120) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcels delivered</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @elseif( $tracking['service'] == 'Prime5' )
                                                        <li class="@if( $this->togglePrime5Status($tracking['api_trackings']) >= 80 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/Direct Link.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Received <br>by Sweden Post</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->togglePrime5Status($tracking['api_trackings']) >= 90) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Customs<br>clearance finalized</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->togglePrime5Status($tracking['api_trackings']) >= 100 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcel in<br> Transit</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->togglePrime5Status($tracking['api_trackings']) >= 110) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Out for  <br> Delivery</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->togglePrime5Status($tracking['api_trackings']) >= 120) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/delivered.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcels Delivered </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        @elseif( $tracking['service'] == 'Total Express' )
                                                        <li class="@if( $this->toggleTotalExpressStatus($tracking['api_trackings']) >= 80 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/total-express-logo.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Received <br>by Total Express</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleTotalExpressStatus($tracking['api_trackings']) >= 90) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Customs<br>clearance finalized</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if($this->toggleTotalExpressStatus($tracking['api_trackings']) >= 100 ) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcel in<br> Transit</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleTotalExpressStatus($tracking['api_trackings']) >= 110) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Out for  <br> Delivery</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="@if( $this->toggleTotalExpressStatus($tracking['api_trackings']) >= 120) active @endif step0">
                                                            <div class="icon-content">
                                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/delivered.png') }}">
                                                                <div class="d-flex flex-column" mt-4>
                                                                    <p class="font-weight-bold">Parcels Delivered </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endif

                                            </ul>
                                        </div>
                                    </div>

                                </div>
                                {{-- <hr> --}}
                                <div class="card">
                                    <div class="table-wrapper position-relative">
                                        <table class="table mb-0 table-responsive-md table-striped" id="">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>@if($tracking['order']->recipient->country_id == \App\Models\Order::US)City @else Country @endif</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tracking['order']->trackings as $track)
                                                    <tr>
                                                        <td>
                                                            {{ $track->created_at }}
                                                        </td>
                                                        <td>
                                                            @if($tracking['order']->recipient->country_id == \App\Models\Order::US) {{ $track->city }} @else {{ $track->country }} @endif
                                                        </td>
                                                        <td>
                                                            {{ $track->description }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if (optional($tracking)['service'] == 'Correios_Brazil')
                                                    <tr>
                                                        <td>
                                                            {{ Carbon\Carbon::createFromFormat('d/m/Y', $tracking['api_trackings']['data'])->format('Y-m-d') }} {{ $tracking['api_trackings']['hora'] }}
                                                        </td>
                                                        <td>
                                                            Brazil
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['descricao'] }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if (optional($tracking)['service'] == 'Correios_Chile')
                                                    <tr>
                                                        <td>
                                                            {{ $tracking['api_trackings']['Fecha'] }}
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['Oficina'] }}
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['Estado'] }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if(optional($tracking)['service'] == 'UPS')
                                                    <tr>
                                                        <td>
                                                        20{{ date('y-m-d', strtotime($tracking['api_trackings']['date'])) }} {{ date('H:i:s', strtotime($tracking['api_trackings']['time'])) }}
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['location']['address']['city'] }}
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['status']['description'] }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if(optional($tracking)['service'] == 'Prime5')
                                                    @foreach($tracking['api_trackings'] as $track)
                                                        <tr>
                                                            <td>
                                                            20{{ date('y-m-d', strtotime($track['DateTime'])) }} {{ date('H:i:s', strtotime($track['DateTime'])) }}
                                                            </td>
                                                            <td>
                                                                {{ optional($track)['LocationText'] }}
                                                            </td>
                                                            <td>
                                                                {{ $track['Description'] }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    
                                                @endif
                                                @if(optional($tracking)['service'] == 'Total Express')
                                                    <tr>
                                                        <td>
                                                            {{ date('y-m-d', strtotime($tracking['api_trackings']['createdAt'])) }} {{ date('H:i:s', strtotime($tracking['api_trackings']['createdAt'])) }}
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                            {{ $tracking['api_trackings']['description'] }} - Status: {{ $tracking['api_trackings']['title'] }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    </div>
                </div>


                @endif
            @endforeach
        </div>

    @endif
    <div wire:loading>
        <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;font-size: 50px;">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </div>
</div>


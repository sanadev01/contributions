<div>
    
    @if ($message)
        <div class="content-header row col-8 offset-2">
            <div class="col-md-12">
                <div class="@if ($status == 201) alert alert-info @endif @if($status == 404) alert alert-warning @endif no-print">
                    <h4>@if ($status == 201) info @endif @if($status == 404) Warning @endif !</h4>
                    <p>{{ $message }}</p>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <input type="text" placeholder="Enter Tracking Number" class="form-control offset-4 col-6 w-100 text-center border border-primary" style="height: 35px; font-size: 20px;" wire:model.defer="trackingNumber">
        </div>
        <div class="col-md-4 col-sm-4">
            <button class="btn btn-primary btn-lg" wire:click="trackOrder">Search</button>
        </div>
    </div>

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
                    
                        <div class="row col-12 d-flex justify-content-between px-3 top">
                            <div class="col-3 d-flex">
                                <h6>HD WHR#: <span class="text-primary font-weight-bold">{{ optional($order)->warehouse_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ optional($order)->corrios_tracking_code }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Piece: <span class="text-primary font-weight-bold">{{ optional($order)->items->count() }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>weight: <span class="text-primary font-weight-bold">{{ optional($order)->weight }} {{ optional($order)->measurement_unit }}</span></h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12" @if($order->recipient->country_id == \App\Models\Order::CHILE) style="margin-left: 30% !important;" @endif>
                                <ul id="progressbar" class="text-center">
                                    @if ($trackingType == 'HD')
                                        <li class="@if($tracking->status_code >=  70) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Freight<br>in transit </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($tracking->status_code >=  73) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/hd-whr.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Received<br>Terminal </p>
                                                </div>
                                            </div>
                                        </li>
                                        {{-- <li class="@if($tracking->status_code >=  75) active @endif step0"></li> --}}
                                        <li class="@if($tracking->status_code >=  75) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/container.png') }}">
                                                <div class="d-flex flex-column mt-4">
                                                    <p class="font-weight-bold">Processed/ <br> manifested </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($tracking->status_code >=  80) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/awb.png') }}">
                                                <div class="d-flex flex-column mt-4">
                                                    <p class="font-weight-bold">@if($tracking->order->recipient->country_id == \App\Models\Order::BRAZIL)Posted @else Shipped @endif</p>
                                                </div>
                                            </div>
                                        </li>
                                        @if ($tracking->order->recipient->country_id != \App\Models\Order::CHILE)
                                        {{-- <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/correios.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Arrived<br>in Country</p>
                                                </div>
                                            </div>
                                        </li> --}}
                                        @endif
                                        <li class="step0">
                                            <div class="icon-content">
                                                @if ($tracking->order->recipient->country_id != \App\Models\Order::CHILE)
                                                    <img class="icon offset-1" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                @else
                                                    <img class="icon offset-1" src="{{ asset('images/tracking/chile-flag.png') }}">
                                                @endif
                                                <div class="d-flex flex-column" mt-4>
                                                    @if ($tracking->order->recipient->country_id != \App\Models\Order::CHILE)
                                                        <p class="font-weight-bold">Received <br>by Correios</p>
                                                    @else
                                                        <p class="font-weight-bold">Received <br>Correios Chile</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                        @if ($tracking->order->recipient->country_id != \App\Models\Order::CHILE)
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Customs clearance<br>Finished</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> in {{ optional(optional(optional($tracking->order)->recipient)->state)->name }} </p>
                                                </div>
                                            </div>
                                        </li>
                                        @endif
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> in @if ($tracking->order->recipient->country_id == \App\Models\Order::CHILE) {{ optional(optional(optional($tracking->order)->recipient)->state)->name }} @endif {{ optional(optional($tracking->order)->recipient)->city }} </p>
                                                </div>
                                            </div>
                                        </li>
                                        @if ($tracking->order->recipient->country_id != \App\Models\Order::CHILE)
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels left  <br> to the buyer </p>
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
                                                    <p class="font-weight-bold">@if($order->recipient->country_id == \App\Models\Order::BRAZIL)Posted @else Shipped @endif</p>
                                                </div>
                                            </div>
                                        </li>
                                        @if ($CorreiosChile == false)
                                        {{-- <li class="@if($posted || $correios_brazil_recieved ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/correios.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Arrived<br>in Country</p>
                                                </div>
                                            </div>
                                        </li> --}}
                                        <li class="@if( $correios_brazil_recieved || $custom_finished || $in_transit || $left_to_buyer || $delivered_to_buyer || $posted ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Received <br>by Correios</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($custom_finished || $in_transit || $left_to_buyer || $delivered_to_buyer || $posted ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/custom-finished.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Customs<br>Finished</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($in_transit || $left_to_buyer || $delivered_to_buyer || $posted ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> in {{optional(optional($order->recipient)->state)->name}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($left_to_buyer || $delivered_to_buyer || $posted ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> in {{optional($order->recipient)->city}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($delivered_to_buyer || $posted) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels left  <br> to the buyer </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($posted ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/delivered.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">parcels delivered </p>
                                                </div>
                                            </div>
                                        </li>
                                        @elseif( $CorreiosChile == true )
                                        <li class="@if( $correios_chile_recieved || $in_transit || $delivered_to_buyer) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/chile-flag.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Received By<br>Correios Chile </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($in_transit || $delivered_to_buyer) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> @if(isset($tracking['Oficina'])) in {{ $tracking['Oficina'] }} @endif</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($delivered_to_buyer) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels delivered</p>
                                                </div>
                                            </div>
                                        </li>
                                        @endif
                                    @endif
                                    
                                </ul>
                            </div>
                        </div>
                </div>
                <hr>
                <div class="card">
                    <div class="table-wrapper position-relative">
                        <table class="table mb-0 table-responsive-md table-striped" id="">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Country</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->trackings as $track)
                                    <tr>
                                        <td>
                                            {{ $track->created_at }}
                                        </td>
                                        <td>
                                            {{ $track->country }}
                                        </td>
                                        <td>
                                            {{ $track->description }}
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($CorreiosBrazil)
                                    <tr>
                                        <td>
                                            {{ $tracking['data'] }} {{ $tracking['hora'] }}
                                        </td>
                                        <td>
                                            Brazil
                                        </td>
                                        <td>
                                            {{ $tracking['descricao'] }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($chileTrackings != null)
                                    @foreach ($chileTrackings as $track)
                                    <tr>
                                        <td>
                                            {{ $track['Fecha'] }}
                                        </td>
                                        <td>
                                            {{ $track['Oficina'] }}
                                        </td>
                                        <td>
                                            {{ $track['Estado'] }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                   
                </div>
            </div>
        </div>
    @endif
    <div wire:loading>
        <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;font-size: 50px;">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </div>
</div>


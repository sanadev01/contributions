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
            <input type="text" placeholder="Enter Tracking Number" class="form-control offset-4 col-8 w-100 text-center border border-primary" style="height: 50px; font-size: 30px;" wire:model.defer="trackingNumber">
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
                            Find Your Order
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
                        <div class="row d-flex justify-content-center ml-5">
                            <div class="col-12">
                                <ul id="progressbar" class="text-center ml-5">
                                    @if (isset($tracking->type) == 'HD' || $HD_Chile == true)
                                        <li class="@if($tracking->status_code >=  70) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Freight<br>in Transit </p>
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
                                                    <p class="font-weight-bold">Manifested </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($tracking->status_code >=  80) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/awb.png') }}">
                                                <div class="d-flex flex-column mt-4">
                                                    <p class="font-weight-bold">Shipped</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Received By<br>Correios Chile </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br>  @if(isset($tracking->Oficina)) in @endif {{ isset($tracking->Oficina) }} </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/left-to-buyer.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels left  <br> to the buyer </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1" src="{{ asset('images/tracking/delivered.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">parcels  <br>delivered to the buyer  </p>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="active step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column mt-2">
                                                    <p class="font-weight-bold">Freight<br>in Transit </p>
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
                                                    <p class="font-weight-bold">Manifested </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="active step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/awb.png') }}">
                                                <div class="d-flex flex-column mt-4">
                                                    <p class="font-weight-bold">Shipped</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if( $correios_chile_recieved || $in_transit || $delivered_to_buyer  ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/brazil-flag.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Received <br>Correios Chile </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($in_transit || $delivered_to_buyer) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/to-hd.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">Parcels in <br> transit to<br>distribution center<br> @if(isset($tracking->Oficina)) in @endif {{ isset($tracking->Oficina) }} </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="@if($delivered_to_buyer ) active @endif step0">
                                            <div class="icon-content">
                                                <img class="icon offset-1 mt-2" src="{{ asset('images/tracking/delivered.png') }}">
                                                <div class="d-flex flex-column" mt-4>
                                                    <p class="font-weight-bold">parcels  <br>delivered to the buyer  </p>
                                                </div>
                                            </div>
                                        </li>
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
                                            @if (isset($track->created_at))
                                                {{ $track->created_at }}
                                            @elseif(isset($track->Fecha)))
                                                {{ $track->Fecha }}    
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @if (isset($track->country))
                                                {{ $track->country }}
                                            @elseif(isset($track->Oficina)))
                                                {{ $track->Oficina }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($track->description))
                                                {{ $track->description }}
                                            @elseif(isset($track->Estado)))
                                                {{ $track->Estado }}
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @endforeach
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


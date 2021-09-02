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
                            Find Your Package
                        </h4>
                        
                    </div>
                    
                        <div class="row col-12 d-flex justify-content-between px-3 top">
                            <div class="col-3 d-flex">
                                <h6>HD WHR#: <span class="text-primary font-weight-bold">{{ optional($tracking->order)->warehouse_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ optional($tracking->order)->corrios_tracking_code }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Piece: <span class="text-primary font-weight-bold">{{ optional(optional($tracking->order)->items)->count() }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>weight: <span class="text-primary font-weight-bold">{{ optional($tracking->order)->weight }} {{ optional($tracking->order)->measurement_unit }}</span></h6>
                            </div>
                            
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12">
                                <ul id="progressbar" class="text-center">
                                    @if ($tracking->type == 'HD')
                                        <li class="@if($tracking->status_code >=  70) active @endif step0"></li>
                                        <li class="@if($tracking->status_code >=  73) active @endif step0"></li>
                                        {{-- <li class="@if($tracking->status_code >=  75) active @endif step0"></li> --}}
                                        <li class="@if($tracking->status_code >=  80) active @endif step0"></li>
                                        <li class="step0"></li>
                                        <li class="step0"></li>
                                    @else
                                        <li class="active step0"></li>
                                        <li class="active step0"></li>
                                        {{-- <li class="active step0"></li> --}}
                                        <li class="active step0"></li>
                                        <li class="@if($tracking->status_code =  01) active @endif step0"></li>
                                        <li class="@if($tracking->status_code =  01) active @endif step0"></li>
                                    @endif
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="row justify-content-between top">
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/to-hd.png') }}">
                                <div class="d-flex flex-column mt-2">
                                    <p class="font-weight-bold">Freight<br>in Transit </p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/hd-whr.png') }}">
                                <div class="d-flex flex-column mt-2">
                                    <p class="font-weight-bold">Received<br>Terminal </p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/container.png') }}">
                                <div class="d-flex flex-column mt-4">
                                    <p class="font-weight-bold">Manifested </p>
                                </div>
                            </div>
                            
                            {{-- <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/to-air.png') }}">
                                <div class="d-flex flex-column" mt-4>
                                    <p class="font-weight-bold">Container goto Airport </p>
                                </div>
                            </div> --}}
                            
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/awb.png') }}">
                                <div class="d-flex flex-column mt-4">
                                    <p class="font-weight-bold">Shipped</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/tracking/correios.png') }}">
                                <div class="d-flex flex-column mt-2">
                                    <p class="font-weight-bold">Arrived<br>in Country</p>
                                </div>
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
                                @foreach ($trackings as $track)
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


@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Get Correios Unit Information</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-2 no-print">
                                <div class="col-12 text-right">
                                    <form action="{{ route('warehouse.unitinfo.create') }}" method="GET">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Select API</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                    <select class="form-control" name="api" id="api" required >
                                                        <option value="">@lang('address.Type')</option>
                                                        <option value="correios" {{ old('api',request('api')) == 'correios' ? 'selected' : '' }}>Correios</option>
                                                        <option value="anjun" {{ old('api',request('api')) == 'anjun' ? 'selected' : '' }}>Anjun</option>
                                                        <option value="bcn" {{ old('api',request('api')) == 'bcn' ? 'selected' : '' }}>Bcn</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Select Type</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                    <select class="form-control" name="type" id="type" required placeholder="@lang('address.Type')">
                                                        <option value="">@lang('address.Type')</option>
                                                        <option value="units_arrival" {{ old('type',request('type')) == 'units_arrival' ? 'selected' : '' }}>Units Arrival Confirmation</option>
                                                        <option value="units_return" {{ old('type',request('type')) == 'units_return' ? 'selected' : '' }}>Available Units for Return</option>
                                                        <option value="confirm_departure" {{ old('type',request('type')) == 'confirm_departure' ? 'selected' : '' }}>Confirmed Departure Units</option>
                                                        <option value="departure_info" {{ old('type',request('type')) == 'departure_info' ? 'selected' : '' }}>Return Departure Information</option>
                                                        <option value="departure_cn38" {{ old('type',request('type')) == 'departure_cn38' ? 'selected' : '' }}> Departure Request CN38</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3" id="s_date">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label id="start_date">Departure Date</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" name="start_date" class="form-control" value="{{old('start_date',request('start_date','2024-01-09T22:55:00Z'))}}" placeholder="2024-01-09T22:55:00Z">
                                                        <div class="help-block float-left ">Please use format (2024-01-09T22:55:00Z)</div>
                                                        @error('start_date')
                                                            <div class="help-block text-danger"> {{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3" id="e_date">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label id="end_date">Arrival Date</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" name="end_date" class="form-control" value="{{old('end_date',request('end_date','2024-01-10T09:49:00Z'))}}" placeholder="2024-01-10T09:49:00Z">
                                                        <div class="help-block float-left">Please use format (2024-01-10T09:49:00Z)</div>
                                                        @error('end_date')
                                                            <div class="help-block text-danger"> {{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-none" id="div_inputs">
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Flight Number</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="flightNo" class="form-control" value="{{old('flightNo',request('flightNo'))}}">
                                                            @error('flightNo')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Airline Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="airlineCode" class="form-control" value="{{old('airlineCode',request('airlineCode'))}}">
                                                            @error('airlineCode')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Departure Airport Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="deprAirportCode" class="form-control" value="{{old('deprAirportCode',request('deprAirportCode'))}}">
                                                            @error('deprAirportCode')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Arrival Airport Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="arrvAirportCode" class="form-control" value="{{old('arrvAirportCode',request('arrvAirportCode'))}}">
                                                            @error('arrvAirportCode')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3" id="destinationCountryCode">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Destination Country Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="destCountryCode" class="form-control" value="{{old('destCountryCode',request('destCountryCode'))}}">
                                                            @error('destCountryCode')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Unit Codes</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <textarea type="textarea" name="unitCode" class="form-control" rows="3"  >{{old('unitCode',request('unitCode'))}}</textarea>
                                                            @error('unitCode')
                                                                <div class="help-block text-danger"> {{ $message }} </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <a href="{{ route('warehouse.unitinfo.create') }}" class="btn btn-secondary btn-md">Clear</a>
                                                <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if($unitInfo)
                                @if($type=='units_arrival')
                                    @include('admin.warehouse.unitInfo.unitsArrival')
                                @elseif($type=='units_return')
                                    @include('admin.warehouse.unitInfo.unitsReturn')
                                @elseif($type=='confirm_departure')
                                    @include('admin.warehouse.unitInfo.confirmDepartureUnits')
                                @elseif($type=='departure_info')
                                    <!-- @include('admin.warehouse.unitInfo.confirmDepartureUnits') -->
                                @endif
                            @endif
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>

@endsection
@section('js')
    <script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            
            $('#type').on('change', function(){
                let type = $(this).val(); 
                if(type == 'departure_info'){
                    
                    $("#start_date").text("Departure Date");
                    $("#end_date").text("Arrival Date");
                    $('#div_inputs').removeClass('d-none');
                    $('#destinationCountryCode').removeClass('d-none');
                }
                else if(type == 'departure_cn38'){
                    $("#start_date").text("Departure Date");
                    $("#end_date").text("Arrival Date");
                    $('#div_inputs').removeClass('d-none');
                    $('#destinationCountryCode').addClass('d-none');
                }else if(type == 'units_return'){
                    $("#s_date").addClass("d-none");
                    $("#e_date").addClass("d-none");
                    $('#div_inputs').addClass('d-none');
                }else {
                    $("#start_date").text("Start Date");
                    $("#end_date").text("End Date");
                    $('#div_inputs').addClass('d-none');
                    $("#s_date").removeClass("d-none");
                    $("#e_date").removeClass("d-none");
                }
            })
            $("#type").trigger('change');
        })
    </script>
@endsection
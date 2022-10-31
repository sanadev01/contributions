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
                                                        <label>Select Type</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                    <select class="form-control" name="type" id="type" required placeholder="@lang('address.Type')">
                                                        <option value="">@lang('address.Type')</option>
                                                        <option value="units_arrival">Units Arrival Confirmation</option>
                                                        <option value="units_return">Available Units for Return</option>
                                                        <option value="departure_info">Return Departure Information</option>
                                                        <option value="confirm_departure">Confirmed Departure Units</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label id="start_date">Start Date</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date" name="start_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label id="end_date">End Date</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date" name="end_date" class="form-control">
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
                                                            <input type="text" name="flightNo" class="form-control">
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
                                                            <input type="text" name="airlineCode" class="form-control">
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
                                                            <input type="text" name="deprAirportCode" class="form-control">
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
                                                            <input type="text" name="arrvAirportCode" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="offset-3 col-md-4">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Destination Country Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="destCountryCode" class="form-control">
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
                                                            <textarea type="textarea" name="unitCode" class="form-control" rows="3"></textarea>
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
                            @if(!empty($unitInfo))
                                @if($type='units_arrival')
                                    @include('admin.warehouse.unitInfo.unitsArrival')
                                @elseif($type='units_return')
                                    @include('admin.warehouse.unitInfo.unitsReturn')
                                @elseif($type='confirm_departure')
                                    @include('admin.warehouse.unitInfo.confirmDepartureUnits')
                                @elseif($type='departure_info')
                                    @include('admin.warehouse.unitInfo.confirmDepartureUnits')
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
                }else {
                    $("#start_date").text("Start Date");
                    $("#end_date").text("End Date");
                    $('#div_inputs').addClass('d-none');
                }
            })
        })
    </script>
@endsection
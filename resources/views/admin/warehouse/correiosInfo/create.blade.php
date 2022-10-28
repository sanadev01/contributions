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
                                    <form action="{{ route('warehouse.unitinfo.store') }}" method="POST" target="_blank">
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
                                                        <option value="departure_units">Confirmed Departure Units</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label>Start Date</label>
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
                                                        <label>End Date</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date" name="end_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="offset-3 col-md-4">
                                                <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if(!empty($unitInfo))
                                @if($type='units_arrival')
                                    @include('admin.warehouse.correiosInfo.unitsArrival')
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
<script>
    $(document).ready(function(){
        $('#type').on('change', function(){
            let type = $(this).val();
            if(val == 'departure_info'){
                
            }
        })
    })
</script>
@section('modal')
<x-modal />
@endsection

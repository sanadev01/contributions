@extends('layouts.master')

@section('page') 
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form">Service Vise Cost and Selling Rates</h4>
            <a href="{{ route('admin.rates.zone-profit.index') }}" class="btn btn-primary pull-right">
                @lang('shipping-rates.Return to List')
            </a>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <form class="form" action="{{ route('admin.rates.uploadRates') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="user_id" id="user_id" wire:model="userId">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h4 class="form-section">Import Service Vise Cost and Selling Rate Sheet</h4>
                            </div>
                        </div>
                        {{-- <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Group <span class="text-danger">*</span></label>
                                        <select name="group_id" id="group" required class="form-control">
                                            <option value="" selected>Select Group</option>
                                            <option value="1">Group 1</option>
                                            <option value="2">Group 2</option>
                                            <option value="3">Group 3</option>
                                            <option value="4">Group 4</option>
                                            <option value="5">Group 5</option>
                                            <option value="6">Group 6</option>
                                            <option value="7">Group 7</option>
                                            <option value="8">Group 8</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Service <span class="text-danger">*</span></label>
                                        <select name="service_id" id="service_id" required class="form-control">
                                            <option value="" selected>Select Service</option>
                                            @foreach($services as $service)
                                                <option value="{{$service->id}}">{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select User</label>
                                    <livewire:components.search-user />
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Rate Type <span class="text-danger">*</span></label>
                                        <select name="type" id="type" required class="form-control">
                                            <option value="Cost" selected>Cost Rate</option>
                                            <option value="Selling" >Selling Rate</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projectinput1">@lang('shipping-rates.Select Excel File to Upload')</label>
                                    <input type="file" class="form-control" name="csv_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    @error('csv_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="alert alert-warning">
                                    <ol>
                                        <li>@lang('shipping-rates.* Upload only Excel files')</li>
                                        <li>@lang('shipping-rates.* Files larger than 15Mb are not allowed')</li>
                                        <li>@lang('shipping-rates.* Download and fill in the data in the sample file below to avoid errors')</li>
                                        <li class="mt-2 dropdown">
                                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 120px;">
                                                @lang('shipping-rates.Download')
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="{{ asset('uploads/Cost Rate Sample.xlsx') }}">Sample File Cost Rates</a>
                                                <a class="dropdown-item" href="{{ asset('uploads/Sell Rate Sample.xlsx') }}">Sample File Selling Rates</a>
                                            </div>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>                        
                    </div>

                    <div class="form-actions pl-5">
                        <a href="{{ route('admin.rates.zone-profit.index') }}" class="btn btn-warning mr-1 ml-3">
                            <i class="ft-x"></i> @lang('shipping-rates.Cancel')
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> @lang('shipping-rates.Import')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
@extends('layouts.app')
@section('custom-css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
    <style>
        .number-input{
            margin-top: 3px;
            padding: 15px;
            font-size: 16px;
            width: 100%;
            border-radius: 3px;
            border: 1px solid #dcdcdc;
        }
    </style>
@endsection
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section id="vue-calculator">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                WorldWide Calculator
                            </h2>
                        </div>
                        <form action="{{route('us-calculator.store')}}" method="POST">
                            @csrf

                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="from_herco">
                                                <input type="checkbox" name="from_herco" id="from_herco" @if(old('from_herco')) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="mt-2">
                                                <label class="h3 text-black" for="from_herco">From HERCO</label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="to_herco">
                                                <input type="checkbox" name="to_herco" id="to_herco" @if(!old('from_herco') && !old('to_international')) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="mt-2">
                                                <label class="h3 text-black" for="to_herco">To HERCO</label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="to_international">
                                                <input type="checkbox" name="to_international" id="to_international" @if(old('to_international')) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="mt-2">
                                                <label class="h3 text-black" for="to_international">To International</label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" id="user_id" value="{{ $userId }}">
                                <div class="row mb-1 mt-3 d-none" id="origin">
                                    <div class="controls col-6">
                                        <h4 class="text-danger">Origin: Homedeliverybr MIA</h4>
                                    </div>
                                </div>
                                <div class="d-none" id="sender_info">
                                    <div class="row mb-1 mt-3">
                                        <div class="controls col-6">
                                            <h4 class="text-danger">Sender Address</h4>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="controls col-4"> 
                                            <label>Origin Country</label>
                                            <select id="origin_country"  name="origin_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.Country')</option>
                                                <option value="250" selected>United States</option>
                                            </select>
                                        </div>
                                        <div class="controls col-4">
                                            <label>Sender State</label>
                                            <option value="" selected disabled hidden>Select State</option>
                                            <select name="sender_state" id="sender_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.State')</option>
                                                @foreach ($states as $state)
                                                    <option {{ old('sender_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="controls col-4">
                                            <label>Sender City</label>
                                            <input type="text" id="sender_city" name="sender_city" value="{{old('sender_city')}}" class="form-control"  required placeholder="Sender City"/>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="controls col-6">
                                            <label>Sender Address</label>
                                            <input type="text" class="form-control" id="sender_address" name="sender_address" value="{{old('sender_address')}}" required placeholder="Sender Address"/>
                                        </div>
                                        <div class="controls col-6">
                                            <label>Sender ZipCode</label>
                                            <input type="text" name="sender_zipcode"  id="sender_zipcode" value="{{ cleanString(old('sender_zipcode')) }}" required class="form-control" placeholder="Zip Code"/>
                                            <div id="sender_zipcode_response">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-none" id="recipient_info">
                                    <div class="row mb-1 mt-3">
                                        <div class="controls col-6">
                                            <h4 class="text-danger">Recipeint Address</h4>
                                        </div>
                                    </div>
                                    <div class="d-none" id="recipient_personal_info">
                                        <div class="row mb-1">
                                            <div class="controls col-4">
                                                <label>Recipient Phone</label>
                                                @livewire('components.search-address', ['user_id' => $userId, 'from_calculator' => true ])
                                            </div>
                                            <div class="controls col-4">
                                                <label>Recipient First Name</label>
                                                <input type="text" id="recipient_first_name" name="recipient_first_name" value="{{old('recipient_first_name')}}" class="form-control"  required placeholder="Recipient first name"/>
                                            </div>
                                            <div class="controls col-4">
                                                <label>Recipient Last Name</label>
                                                <input type="text" id="recipient_last_name" name="recipient_last_name" value="{{old('recipient_last_name')}}" class="form-control"  required placeholder="Recipient last name"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="controls col-4" id="all_destination_countries"> 
                                            <label>Destination Country</label>
                                            <select id="destination_country"  name="destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.Country')</option>
                                                @foreach (countries() as $country)
                                                    <option {{ old('destination_country') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="controls col-4" id="us_destination_country"> 
                                            <label>Destination Country</label>
                                            <select id="us_destination_country"  name="us_destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.Country')</option>
                                                <option value="250" selected>United States</option>
                                            </select>
                                        </div>
                                        <div class="controls col-4" id="all_destination_states"> 
                                            <label>Recipient State</label>
                                            <option value="" selected disabled hidden>Select State</option>
                                            <select name="recipient_state" id="recipient_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.State')</option>
                                                @foreach (states() as $state)
                                                    <option {{ old('recipient_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="controls col-4" id="us_destination_states"> 
                                            <label>Recipient State</label>
                                            <option value="" selected disabled hidden>Select State</option>
                                            <select name="us_recipient_state" id="us_recipient_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                                <option value="">Select @lang('address.State')</option>
                                                @foreach ($states as $state)
                                                    <option {{ old('sender_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="controls col-4">
                                            <label>Recipient City</label>
                                            <input type="text" id="recipient_city" name="recipient_city" value="{{old('recipient_city')}}" class="form-control"  required placeholder="Recipient City"/>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="controls col-6">
                                            <label>Recipient Address</label>
                                            <input type="text" class="form-control" id="recipient_address" name="recipient_address" value="{{old('recipient_address')}}" required placeholder="Recipient Address"/>
                                        </div>
                                        <div class="controls col-6">
                                            <label>Recipient ZipCode</label>
                                            <input type="text" name="recipient_zipcode"  id="recipient_zipcode" value="{{ cleanString(old('recipient_zipcode')) }}" required class="form-control" placeholder="Zip Code"/>
                                            <div id="recipient_zipcode_response"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1 mt-3 d-none" id="destination">
                                    <div class="controls col-6">
                                        <h4 class="text-danger">Destination: Homedeliverybr MIA</h4>
                                    </div>
                                </div>

                                <div class="row mb-1 mt-3">
                                    <div class="controls col-6">
                                        <h4 class="text-danger">Shipment Info :</h4>
                                    </div>
                                </div>
                                <div class="row d-none" id="calculator-items">
                                    <livewire:calculator.items>
                                </div>
                                <livewire:calculator.calculation :cc='$cc'>

                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            Get Rates
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('jquery')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('uscalculator.script')
@include('uscalculator.searchAddress')

@endsection
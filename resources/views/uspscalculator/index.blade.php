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

    <!-- Dashboard Analytics Start -->
    <section id="vue-calculator">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                USPS Calculator
                            </h2>
                        </div>
                        
                    <form action="{{action('USPSCalculatorController@store')}}" method="POST">

                        @csrf
                        <div class="card-body">
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
                            <div class="row mb-1 mt-3">
                                <div class="controls col-6">
                                    <h4 class="text-danger">Recipeint Address</h4>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="controls col-4"> 
                                    <label>Destination Country</label>
                                    <select id="destination_country"  name="destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.Country')</option>
                                        @foreach (countries() as $country)
                                            <option {{ old('destination_country') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="controls col-4"> 
                                    <label>Recipient State</label>
                                    <option value="" selected disabled hidden>Select State</option>
                                    <select name="recipient_state" id="recipient_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.State')</option>
                                        @foreach (states() as $state)
                                            <option {{ old('recipient_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
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
                            <div class="row mb-1 mt-3">
                                <div class="controls col-6">
                                    <h4 class="text-danger">Shipment Info :</h4>
                                </div>
                            </div>
                            <div class="row d-none" id="calculator-items">
                                <livewire:calculator.items>
                            </div>
                            
                            <livewire:calculator.calculation>

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


    <!-- Dashboard Analytics end -->
@endsection
@section('jquery')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('uspscalculator.script')
@include('uspscalculator.states-script')
@endsection
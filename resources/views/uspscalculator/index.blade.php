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
                <div class="col-8">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                USPS Calculator
                            </h2>
                        </div>
                        
                    <form action="{{action('USPSCalculatorController@store')}}" method="POST">

                        @csrf
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="controls col-6"> 
                                    <label>Origin Country</label>
                                    <select id="origin_country"  name="origin_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.Country')</option>
                                        @foreach (countries() as $country)
                                            <option {{ old('origin_country') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="controls col-6"> 
                                    <label>Destination Country</label>
                                    <select id="destination_country"  name="destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.Country')</option>
                                        <option  {{ old('destination_country') == 250 ? 'selected' : ''}} value="250">United States</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="controls col-6">
                                    <label>Destination State</label>
                                    <option value="" selected disabled hidden>Select State</option>
                                    <select name="destination_state" id="destination_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.State')</option>
                                        @foreach ($states as $state)
                                            <option {{ old('destination_state') == $state->id ? 'selected' : '' }} value="{{ $state->id }}">{{ $state->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="controls col-6">
                                    <label>Destination Address</label>
                                    <input type="text" class="form-control" id="destination_address" name="destination_address" value="{{old('destination_address')}}" required placeholder="Destination Address"/>
                                </div>
                            </div>

                            <div class="row mb-1">
                                <div class="controls col-6">
                                    <label>Destination City</label>
                                    <input type="text" id="destination_city" name="destination_city" value="{{old('destination_city')}}" class="form-control"  required placeholder="Destination City"/>
                                </div>
                                <div class="controls col-6">
                                    <label>Destination ZipCode</label>
                                    <input type="text" name="destination_zipcode"  id="destination_zipcode" value="{{ cleanString(old('destination_zipcode')) }}" required class="form-control" placeholder="Zip Code"/>
                                    <div id="zipcode_response">
                                        
                                    </div>
                                </div>
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
                        {{-- <div>
                            <div class="row justify-content-center">
                                <div class="col-md-8 text-center">
                                    <button class="btn btn-lg btn-primary">
                                        Re-Calculate
                                    </button>
                                </div>
                            </div>
                        </div> --}}
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
@endsection
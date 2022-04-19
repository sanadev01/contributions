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
                                UPS Calculator
                            </h2>
                        </div>
                        
                    <form action="{{action('UPSCalculatorController@store')}}" method="POST">

                        @csrf
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="controls col-6"> 
                                    <label>Origin Country</label>
                                    <select id="origin_country"  name="origin_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.Country')</option>
                                        <option value="250" selected>United States</option>
                                    </select>
                                </div>
                                <div class="controls col-6"> 
                                    <label>Destination Country</label>
                                    <select id="destination_country"  name="destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.Country')</option>
                                        <option value="250" selected>United States</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="controls col-6">
                                    <label>Sender State</label>
                                    <option value="" selected disabled hidden>Select State</option>
                                    <select name="sender_state" id="sender_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                        <option value="">Select @lang('address.State')</option>
                                        @foreach ($states as $state)
                                            <option {{ old('sender_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="controls col-6">
                                    <label>Sender Address</label>
                                    <input type="text" class="form-control" id="sender_address" name="sender_address" value="{{old('sender_address')}}" required placeholder="Sender Address"/>
                                </div>
                            </div>

                            <div class="row mb-1">
                                <div class="controls col-6">
                                    <label>Sender City</label>
                                    <input type="text" id="sender_city" name="sender_city" value="{{old('sender_city')}}" class="form-control"  required placeholder="Sender City"/>
                                </div>
                                <div class="controls col-6">
                                    <label>Sender ZipCode</label>
                                    <input type="text" name="sender_zipcode"  id="sender_zipcode" value="{{ cleanString(old('sender_zipcode')) }}" required class="form-control" placeholder="Zip Code"/>
                                    <div id="zipcode_response">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-1 mt-3">
                                <div class="controls col-6">
                                    <h4 class="text-danger">Destination : Homedeliverybr MIA</h4>
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
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Dashboard Analytics end -->
@endsection
@section('jquery')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('upscalculator.script')
@endsection
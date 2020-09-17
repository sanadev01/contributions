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
                                Calculator
                            </h2>
                        </div>
                        
                    <form action="{{action('CalculatorController@store')}}" method="POST">

                        @csrf
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="controls col-12"> 
                                    <label>Destination Country</label>
                                    <select id="country"  name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                        <option value="">Select @lang('address.Country')</option>
                                        @foreach (countries() as $country)
                                            <option {{ old('country_id') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="controls col-12">
                                    <label>Destination State</label>
                                    <option value="" selected disabled hidden>Select State</option>
                                    <select name="state_id" id="state" class="form-control selectpicker show-tick" data-live-search="true">
                                        <option value="">Select @lang('address.State')</option>
                                    </select>
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
@include('layouts.states-ajax')
@endsection
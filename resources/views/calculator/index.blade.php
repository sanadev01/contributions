@extends('layouts.app')
@section('custom-css')
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

                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="controls col-12">
                                    <label>Destination Country</label>
                                    <select name="" id="" class="form-control">
                                        <option value="" selected disabled hidden>Select Country</option>
                                        @isset($countries)
                                            @foreach ($countries as $country)
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="controls col-12">
                                    <label>Destination State</label>
                                    <option value="" selected disabled hidden>Select State</option>
                                    <select name="" id="" class="form-control">
                                        @isset($states)
                                            @foreach ($states as $state)
                                                <option value="{{$state->id}}">{{$state->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="form-group col-12">
                                    <div class="controls">
                                        <label>Measuring Units <span class="text-danger">*</span></label>
                                        <select name="" id="" class="form-control">
                                            <option value="" selected disabled hidden>Select Measuring Units</option>
                                            <option value="lbs/in">lbs/in</option>
                                            <option value="kg/cm">kg/cm</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="form-group col-12">
                                    <div class="controls">
                                        <label>Weight (lbs) <span class="text-danger">*</span></label>
                                        <input type="number" class="number-input" required name="weight"  placeholder="">
                                        <div class="help-block">
                                            <span>0.000 </span>
                                            <span>kg</span>
                                            {{-- <span>lbs</span> --}}
                                            <strong class="text-danger">Or 0.0 Ounces</strong>
                                            <strong class="text-warning">Or 0.00 Grams</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="row mb-1">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>Length (in)<span class="text-danger">*</span></label>
                                        <input type="number" class="number-input" required name="length"  placeholder="">
                                        <div class="help-block">
                                            <span>0.000</span>
                                            {{-- <span>in</span> --}}
                                            <span>cm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>Width (in) <span class="text-danger">*</span></label>
                                        <input type="number" class="number-input" required name="width"  placeholder="">
                                        <div class="help-block">
                                            <span>0.000</span>
                                            {{-- <span>in</span> --}}
                                            <span>cm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>Height (in) <span class="text-danger">*</span></label>
                                        <input type="number" class="number-input" required name="height" placeholder="">
                                        <div class="help-block">
                                            <span>0.000</span>
                                            {{-- <span>in</span> --}}
                                            <span>cm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="controls h2 mt-2">
                                        <label>
                                            <span class="text-danger">*</span>
                                            The Rate will be applied on
                                            <strong class="text-danger h2">
                                                0
                                                <span class="ml-1"> lbs</span>
                                            </strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                    <button class="btn btn-primary btn-lg">
                                        <i class="fa fa-spinner fa-spin"></i>
                                        Get Rates
                                    </button>
                                </div>
                            </div>
                        </div>
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

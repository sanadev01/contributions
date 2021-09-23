@extends('layouts.app')
@section('content')
    <!-- Dashboard Analytics Start -->
    <section id="vue-calculator">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                Rate Calculated For USPS
                            </h2>
                        </div>

                        <div class="col-md-12">
                            <x-flash-message></x-flash-message>
                        </div>
                         
                        <div class="card-body">
                            @if ($shipping_rates != null)
                                <div class="text-center">
                                    @foreach ($shipping_rates as $shipping_rate) 
                                        <div class="card-body"><div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Service Name
                                        </div> <div class="border col-5 py-1">
                                            {{$shipping_rate['name']}}
                                        </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Weight
                                        </div> <div class="border col-5 py-1">
                                            @if($order->measurement_unit == 'kg/cm')
                                                {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                                            @else
                                                {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                                            @endif
                                        </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Cost
                                        </div> <div class="border col-5 py-1 text-danger h2">

                                            {{$shipping_rate['rate']}} USD
                                        
                                            <br>
                                        
                                        </div></div></div></div></div>
                                        <hr>
                                    @endforeach
                                </div>
                            @endif
                            <br>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                <a href="{{route('usps-calculator.index')}}" class="btn btn-primary btn-lg">
                                        Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin'))
                        <div class="card p-2">
                            <div class="card-header pb-0">
                                <h2 class="mb-2 text-center w-100">
                                    Rate Calculated For USPS (without Profit)
                                </h2>
                            </div>

                            <div class="col-md-12">
                                <x-flash-message></x-flash-message>
                            </div>

                            <div class="card-body">
                                @if ($usps_rates != null)
                                    <div class="text-center">
                                        @foreach ($usps_rates as $usps_rate) 
                                            <div class="card-body"><div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Service Name
                                            </div> <div class="border col-5 py-1">
                                                {{$usps_rate['name']}}
                                            </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Weight
                                            </div> <div class="border col-5 py-1">
                                                @if($order->measurement_unit == 'kg/cm')
                                                    {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                                                @else
                                                    {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                                                @endif
                                            </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Cost
                                            </div> <div class="border col-5 py-1 text-danger h2">

                                                {{$usps_rate['rate']}} USD
                                            
                                                <br>
                                            
                                            </div></div></div></div></div>
                                            <hr>
                                        @endforeach
                                    </div>
                                @endif
                                <br>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                    <a href="{{route('usps-calculator.index')}}" class="btn btn-primary btn-lg">
                                            Go Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Analytics end -->
@endsection

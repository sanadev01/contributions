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
                                Rate Calculated
                            </h2>
                        </div>
                        @if ($shippingServices->isEmpty())
                            <div class="col-md-12">
                                <x-flash-message></x-flash-message>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="text-center">
                                @foreach ($shippingServices as $shippingService) 
                                    <div class="card-body"><div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                        Service Name
                                    </div> <div class="border col-5 py-1">
                                        {{$shippingService->name}}
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
                                        
                                        {{$shippingService->getRateFor($order,true,true)}} USD
                                       
                                        <br>
                                       
                                    </div></div></div></div></div>
                                    <hr>
                                @endforeach
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                <a href="{{route('calculator.index')}}" class="btn btn-primary btn-lg">
                                        Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Analytics end -->
@endsection

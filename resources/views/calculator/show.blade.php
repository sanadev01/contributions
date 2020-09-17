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

                        @csrf
                        <div class="card-body">
                            
                            <div class="text-center">
                            @foreach ($shippingServices as $shippingService)


                                <p><strong>Shipping Service:</strong> {{$shippingService->name}}</p>
                                <p><strong>Service Rate:</strong> {{$shippingService->getRateFor($order)}}</p>
                                <p><strong>Total Weight:</strong> {{$order->weight}}</p>

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

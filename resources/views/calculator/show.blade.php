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
                                Rates Calculated
                            </h2>
                        </div>

                        @csrf
                        <div class="card-body">
                            
                            @foreach ($shippingServices as $shippingService)
                                {{$shippingService->name}}
                                {{$shippingService->getRateFor($order)}}
                                {{$order->weight}}
                            @endforeach

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Analytics end -->
@endsection

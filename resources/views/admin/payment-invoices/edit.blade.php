@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Edit Invoice
                        </h4>
                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if( $errors->count() )
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('admin.payment-invoices.invoice.update',$invoice) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <h2 class="mt-2">Orders in Invoice</h2>
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="grid-wrapper w-auto">

                                            @foreach ($invoice->orders as $order)
                                                <div class="card-wrapper h-auto my-2 w-auto">
                                                    <input class="c-card" type="checkbox" name="orders[]" id="{{$order->id}}" checked value="{{$order->id}}">
                                                    <div class="card-content">
                                                        <div class="card-state-icon"></div>
                                                        <label for="{{$order->id}}">
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Merchant:</strong> <span class="text-info">{{ $order->merchant }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Customer Refrence:</strong> <span class="text-info">{{ $order->customer_reference }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Tracking ID:</strong> <span class="text-info">{{ $order->tracking_id }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Tracking Code</strong> <span class="text-info">{{  $order->corrios_tracking_code }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h2 class="mt-4">Order Available to Pay</h2>
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="grid-wrapper w-auto">

                                            @foreach ($orders as $order)
                                                <div class="card-wrapper h-auto my-2 w-auto">
                                                    <input class="c-card" type="checkbox" name="orders[]" id="{{$order->id}}" {{ request('order') == $order->id ? 'checked': '' }} value="{{$order->id}}">
                                                    <div class="card-content">
                                                        <div class="card-state-icon"></div>
                                                        <label for="{{$order->id}}">
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Merchant:</strong> <span class="text-info">{{ $order->merchant }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Customer Refrence:</strong> <span class="text-info">{{ $order->customer_reference }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Tracking ID:</strong> <span class="text-info">{{ $order->tracking_id }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Tracking Code</strong> <span class="text-info">{{  $order->corrios_tracking_code }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-8 text-right">
                                        <button class="btn btn-primary btn-lg">View Invoice</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

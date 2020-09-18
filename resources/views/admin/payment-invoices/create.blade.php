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
                            Select Orders To Pay
                        </h4>
                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <p class="h5">Here you can select multiple unpaid orders to pay in one invoice.  if you want to pay one by one just select only one order and click view invoice.</p>
                        <hr>
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
                            <form action="{{ route('admin.payment-invoices.orders.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

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
                                                                <strong class="border-bottom-dark mr-2">Recipient:</strong> <span class="text-info">{{ optional($order->recipient)->first_name }} {{ optional($order->recipient)->last_name }}</span>
                                                            </div>
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
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">WHR#</strong> <span class="text-info">{{  $order->warehouse_number }}</span>
                                                            </div>
                                                            <div class="h5 py-1 px-2">
                                                                <strong class="border-bottom-dark mr-2">Value</strong> <span class="text-info">{{  $order->gross_total }}USD</span>
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

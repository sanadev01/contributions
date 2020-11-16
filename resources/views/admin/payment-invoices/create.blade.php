@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('invoice.Select Orders To Pay')
                        </h4>
                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">
                            @lang('invoice.Back to List')
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p class="h5 dim">@lang('invoice.Invoice Message')</p>
                            <hr>
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
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>#</th>
                                                    <th>@lang('invoice.Recipient')</th>
                                                    <th>@lang('invoice.Merchant')</th>
                                                    <th>@lang('invoice.Customer Refrence')</th>
                                                    <th>@lang('invoice.Tracking ID')</th>
                                                    <th>@lang('invoice.Tracking Code')</th>
                                                    <th>@lang('invoice.WHR')#</th>
                                                    <th>@lang('invoice.Value')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orders as $order)
                                                    <tr class="selectable cursor-pointer {{ request('order') == $order->id ? 'bg-info' : '' }}">
                                                        <td>
                                                            <input class="form-control" type="checkbox" name="orders[]" id="{{$order->id}}" {{ request('order') == $order->id ? 'checked': '' }} value="{{$order->id}}">
                                                        </td>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            {{ optional($order->recipient)->first_name }} {{ optional($order->recipient)->last_name }}
                                                        </td>
                                                        <td>{{ $order->merchant }}</td>
                                                        <td>{{ $order->customer_reference }}</td>
                                                        <td>{{ $order->tracking_id }}</td>
                                                        <td>{{  $order->corrios_tracking_code }}</td>
                                                        <td>{{  $order->warehouse_number }}</td>
                                                        <td>{{  number_format($order->gross_total,2) }} USD</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-8 text-right">
                                        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-lg">@lang('invoice.Add More Orders')</a>
                                        <button class="btn btn-primary btn-lg">@lang('invoice.Pay Orders')</button>
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
@section('js')
    <script>
        $('tr.selectable').on('click',function(){
            if ( $(this).find('input[type="checkbox"]').attr('checked') ){
                $(this).removeClass('bg-info');
                $(this).find('input[type="checkbox"]').attr('checked',false)
            }else{
                $(this).addClass('bg-info');
                $(this).find('input[type="checkbox"]').attr('checked',true)
            }
        });
    </script>
@endsection
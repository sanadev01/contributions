@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endsection
@section('page')

@if($order->hasSecondLabel())
<div class="card pb-3">
    <div class="row mr-3">
        <div class="ml-auto mt-5">
            <button onclick="window.open('{{ route('order.us-label.download',[$order,'time'=>md5(microtime())]) }}','','top:0,left:0,width:600px;height:700px;')" class="btn btn-primary">Download</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary ml-2 pull-right">
                @lang('shipping-rates.Return to List')
            </a>
        </div>
    </div>
    <div class="container">
        <div class="label mt-2">
            <iframe src="https://docs.google.com/gview?url={{ route('order.us-label.download',$order) }}&embedded=true&time{{md5(microtime())}}" style="width:100%; height:700px;" frameborder="0">
                <iframe src="{{ route('order.us-label.download',$order) }}" style="width:100%; height:700px;" frameborder="0"></iframe>
            </iframe>
        </div>
    </div>
</div>
@else
    @livewire('order.us-label-form', ['order' => $order, 'states' => $states, 'usShippingServices' => $usShippingServices, 'errors' => $errors])
@endif

@endsection

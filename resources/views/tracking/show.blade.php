@extends('layouts.app')
@section('css')
<style>


.card {
    z-index: 0; 
}

.top {
    padding-top: 40px;
    padding-left: 17% !important;
    padding-right: 14% !important
}

#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    color: #455A64;
    padding-left: 0px;
    margin-top: 30px
}

#progressbar li {
    list-style-type: none;
    font-size: 13px;
    width: 33%;
    float: left;
    position: relative;
    font-weight: 400
}

#progressbar .step0:before {
    font-family: FontAwesome;
    content: "\f10c";
    color: #fff
}

#progressbar li:before {
    width: 40px;
    height: 40px;
    line-height: 45px;
    display: block;
    font-size: 20px;
    background: #C5CAE9;
    border-radius: 50%;
    margin: auto;
    padding: 0px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 12px;
    background: #C5CAE9;
    position: absolute;
    left: 0;
    top: 16px;
    z-index: -1
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    position: absolute;
    left: -50%
}

#progressbar li:nth-child(2):after,
#progressbar li:nth-child(3):after,
#progressbar li:nth-child(4):after {
    left: -50%
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    position: absolute;
    left: 50%
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #651FFF
}

#progressbar li.active:before {
    font-family: FontAwesome;
    content: "\f00c"
}

.icon {
    width: 60px;
    height: 60px;
    margin-right: 15px
}

.icon-content {
    padding-bottom: 20px
}

@media screen and (max-width: 992px) {
    .icon-content {
        width: 50%
    }
}
</style>
@endsection
@section('content')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Tracking Package
                        </h4>
                        <div>
                            <a href="{{ route('admin.tracking.index') }}" class="btn btn-primary"> <i class="fa fa-search"></i> Tracking Package </a>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="row col-12 d-flex justify-content-between px-3 top">
                            <div class="col-3 d-flex">
                                <h6>ORDER ID: <span class="text-primary font-weight-bold">{{ $order->warehouse_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Tracking Number: <span class="text-primary font-weight-bold">{{ $order->corrios_tracking_code }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>Dispatch Number: <span class="text-primary font-weight-bold">{{ optional(optional($order->containers)[0])->dispatch_number }}</span></h6>
                            </div>
                            <div class="col-3 d-flex">
                                <h6>AWB#: <span class="text-primary font-weight-bold">{{ optional(optional($order->containers)[0])->awb }}</span></h6>
                            </div>
                            
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-12">
                                <ul id="progressbar" class="text-center">
                                    <li class="@if($order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li>
                                    <li class="@if($order->status >= App\Models\Order::STATUS_PAYMENT_DONE ) active @endif step0"></li>
                                    <li class="@if($order->status >= App\Models\Order::STATUS_SHIPPED ) active @endif step0"></li>
                                    {{-- <li class="@if($order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li>
                                    <li class="@if($order->status >= App\Models\Order::STATUS_ORDER ) active @endif step0"></li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="row justify-content-between top">
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/order.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Processed</p>
                                </div>
                            </div>
                            {{-- <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/GiWFtVu.png">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Designing</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="https://i.imgur.com/u1AzR7w.png">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Shipped</p>
                                </div>
                            </div> --}}
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/onway.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>En Route</p>
                                </div>
                            </div>
                            <div class="row d-flex icon-content"> <img class="icon" src="{{ asset('images/shipped.png') }}">
                                <div class="d-flex flex-column">
                                    <p class="font-weight-bold">Order<br>Shipped</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

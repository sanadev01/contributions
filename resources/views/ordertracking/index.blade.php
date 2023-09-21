@extends('layouts.app')
@section('css')
<style>


.card {
    z-index: 0; 
}

.top {
    padding-top: 40px;
    padding-left: 5.5% !important;
    padding-right: 3.5% !important
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
    width: 9%;
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
#progressbar li:nth-child(4):after,
#progressbar li:nth-child(5):after,
#progressbar li:nth-child(6):after,
#progressbar li:nth-child(7):after,
#progressbar li:nth-child(8):after,
#progressbar li:nth-child(9):after,
#progressbar li:nth-child(10):after,
#progressbar li:nth-child(11):after {
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
    width: 33px;
    height: 33px;
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
    <div class="">
        <div class="row">
            <div class="col-md-12 col-sm-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Track Your Order
                        </h4>
                        
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:order-tracking.trackings>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
    
</script>
@endsection

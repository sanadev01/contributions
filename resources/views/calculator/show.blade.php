@extends('layouts.master')
@section('css')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">

<style>
    .breadcrumb-bg {
        background-color: #f7fbfe;
    }

    .card-bg {
        color: #373C3F;
        border: 1px solid #ffffff;
        background-color: #ffffff;
    }

    .btn-blue {
        background-color: #1174b7;
        color: white;
    }

    .rate-category {
        background-color: #e9f1ee;
        color: #347b87;
    }
    .standard-font{
         font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .color-gray{
        color: #6c757d;
    }
    .star-rating {
      unicode-bidi: bidi-override;
      font-size: 18px;
      color: #ffd700;
      margin-bottom: 10px;
    }
    .star-rating span {
      padding-right: 2px;
    }
</style>
@endsection
@section('page')
<div class="float-right">
    <a href="{{route('calculator.index')}}" class="btn btn-md rounded px-5 my-3" style="background-color: #7367f0;color: #fff;">
        <i class="fas fa-arrow-left"></i>
        Go Back</a>
</div>
<section>
    <nav>
        <ol class="breadcrumb breadcrumb-bg">
            <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="/calculator">Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">Correios</li>
        </ol>

    </nav>

</section>
<section>
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="font-weight-bold dt">Rate Calculated</h4>
        </div>
    </div>
</section>
<section>
    <input type="text" id="searchInput" class="form-control col-6 my-4" placeholder=" Search...">
    <table class="table table-borderless p-0 table-responsive-md table-striped" id="kpi-report">
        <thead>
            <tr id="kpiHead">
                <th class="py-3 font-black">@lang('orders.Courier')</th>
                <th class="py-3 font-black">@lang('orders.Rating')</th>
                <th class="py-3 font-black">@lang('orders.Average Transit')</th>
                <!-- <th class="py-3 font-black">@lang('orders.Tracking')</th> -->
                <!-- <th class="py-3 font-black">@lang('orders.Weight')</th> -->
                <!-- <th class="py-3 font-black">@lang('orders.Service Options')</th> -->
                <!-- <th class="py-3 font-black">@lang('orders.Import Tax')</th> -->
                <th class="py-3 font-black">@lang('orders.Total Cost')</th>
            </tr>
        </thead>
        <tbody>
            @if($chargableWeight>0)
            @foreach ($shippingServices as $shippingService)
            @if(checksSettingShippingService($shippingService))
            <tr>
                <td>
                    <img width="30" height="30" class="corrioes-lable" src="{{ asset('images/tracking/' . $shippingService->carrier_service . '.png') }}">
                    {{$shippingService->sub_name}}
                </td>
                <td>
                    <div class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $shippingService->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </td>
                <td>{{ $shippingService->delivery_time}}</td>
                <td class="price-tag">
                    {{$shippingService->getRateFor($order,true,true)}} USD
                </td>
            </tr>
            @endif
            @endforeach
            @endif


        </tbody>
    </table>
    <br>


</section> 
@endsection
@section('js')
<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#kpi-report tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
@endsection
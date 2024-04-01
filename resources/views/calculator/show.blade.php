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
</style>
@endsection
@section('page')
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
        <div class="col-12 mx-2">
            <h4 class="font-weight-bold dt">Rate Calculated</h4>
        </div>
    </div>
</section>
<section>
    <table class="table  table-borderless p-0 table-responsive-md table-striped  " id="kpi-report">
        <thead>
            <tr id="kpiHead">
                <th class="py-3 font-black">@lang('orders.Courier')</th>
                <th class="py-3 font-black">@lang('orders.Speciality')</th>
                <th class="py-3 font-black">@lang('orders.Delivery Time')</th>
                <!-- <th class="py-3 font-black">@lang('orders.Tracking')</th> -->
                <th class="py-3 font-black">@lang('orders.Weight')</th>
                <!-- <th class="py-3 font-black">@lang('orders.Service Options')</th> -->
                <!-- <th class="py-3 font-black">@lang('orders.Import Tax')</th> -->
                <th class="py-3 font-black">@lang('orders.Total Cost')</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($shippingServices as $shippingService)

            <tr>
                <td>{{$shippingService->name}}</td>
                <td class="category-tag"></td>
                <td>{{ $shippingService->delivery_time}}</td>
                <!-- <td></td> -->
                <td> @if($order->measurement_unit == 'kg/cm')
                    {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                    @else
                    {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                    @endif
                </td>
                <!-- <td></td>
                <td></td> -->
                <td class="price-tag">
                    {{$shippingService->getRateFor($order,true,true)}} USD
                </td>
            </tr>
            @endforeach


        </tbody>
    </table>
    <br>
    <div class="row ml-1 mt-3">
        <a href="{{route('calculator.index')}}" class="btn btn-blue btn-md rounded px-5 my-3">
            Go Back
        </a>
    </div>


</section>
<!-- Dashboard Analytics end -->
@endsection
@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const priceTags = document.querySelectorAll(".price-tag");
        const rates = Array.from(priceTags).map(tag => parseFloat(tag.textContent));

        const cheapestRate = Math.min(...rates);
        const mostExpensiveRate = Math.max(...rates);
        const middleRate = rates.sort((a, b) => a - b)[Math.floor(rates.length / 2)];

        const categoryTags = document.querySelectorAll(".category-tag");
        priceTags.forEach((tag, index) => {
            const rate = parseFloat(tag.textContent);
            let category = "";
            if (rate === cheapestRate) {
                category = "<span class='px-2 py-1 rate-category' style='font-size: 1.2em;'>Cheapest</span>";
            } else if (rate === mostExpensiveRate) {
                category = "<span class='px-2 py-1 rate-category' style='font-size: 1.2em;'>Most Expensive</span>";
            } else if (rate === middleRate) {
                category = "<span class='px-2 py-1 rate-category' style='font-size: 1.2em;'>Middle</span>";
            } else if (rate < middleRate) {
                category = "<span class='px-2 py-1 rate-category' style='font-size: 1.2em;'>Best</span>";
            } else if (rate > middleRate) {
                category = "<span class='px-2 py-1 rate-category' style='font-size: 1.2em;'>Expensive</span>";
            }
            categoryTags[index].innerHTML = category;
        });
    });
</script>
@endsection
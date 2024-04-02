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

<section>
    <div class="float-right">
        <a href="@if($shippingServiceTitle == 'UPS') {{route('ups-calculator.index')}} @else {{route('us-calculator.index')}} @endif" class="btn btn-blue btn-md rounded px-5">
            <i class="fas fa-arrow-left"></i>
            Go Back
        </a>
    </div>
    <nav>
        <ol class="breadcrumb breadcrumb-bg">
            <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="/calculator">Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$shippingServiceTitle}}</li>
        </ol>
    </nav>

    <div class="row mt-4">
        <div class="col-12 mx-2">
            <div class="d-flex justify-content-between my-3">
                <div>
                    <h4 class="font-weight-bold dt">Rate Calculated</h4>
                </div>
                <div>
                    @if ($ratesWithProfit)
                    <div href="#" wire:click="downloadRates" class="rounded-circle border border-success bg-white d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;" title="Download rates">
                        <i class="fas fa-download text-success"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <table class="table  table-borderless p-0 table-responsive-md table-striped" id="kpi-report">
        <thead>
            <tr id="kpiHead">
                <th class="py-3 font-black">@lang('orders.Courier')</th>
                <th class="py-3 font-black">@lang('orders.Speciality')</th>
                <th class="py-3 font-black">@lang('orders.Weight')</th>
                @if(auth()->user()->hasRole('admin')) <th>@lang('orders.Actual Cost')</th> @endif
                <th class="py-3 font-black">@lang('orders.Total Cost')</th>
                <th class="py-3 font-black">@lang('orders.actions.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ratesWithProfit as $key=>$profitRate)
            <tr>
                <td>
                    <img width="30" height="30" class="corrioes-lable" src="{{ asset('images/tracking/' . (\App\Models\ShippingService::where('name',$profitRate['name'])->first())->carrier_service . '.png') }}">
                    {{$profitRate['name']}}
                </td>
                <td class="category-tag"></td>
                <td>
                    @if($tempOrder['measurement_unit'] == 'kg/cm')
                    {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                    @else
                    {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                    @endif
                </td>

                @if(auth()->user()->hasRole('admin')) <td>{{$apiRates[$key]['rate']}} USD</td> @endif
                <!--<td></td> -->
                <td class="price-tag">
                    {{$profitRate['rate']}} USD
                </td>
                <td>
                    @if($userLoggedIn)
                    @if($serviceResponse)
                    <div class="row mb-1 ml-4">
                        <div class="controls col-12">
                        </div>
                    </div>
                    @endif
                    @error($serviceError)
                    <div class="row mb-1 ml-4">
                        <div class="controls col-12 text-danger">
                            {{$message}}
                        </div>
                    </div>
                    @enderror
                    @error('selectedService')<div class="row mb-1 ml-4"> <span class="error text-danger">{{ $message }}</span> </div>@enderror
                    <button id="btn-submit" wire:click="getLabel('{{ $profitRate['service_sub_class'] }}')" type="submit" class="btn btn-success btn-sm  "><i class="fas fa-print text-print mx-2"></i>Buy Label</button>
                    @endif
                </td>
            </tr>
            @endforeach


        </tbody>
    </table>
</section>
<!-- Dashboard Analytics end -->
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
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

<section>
    <div class="float-right">
        <a href="@if($shippingServiceTitle == 'UPS') {{route('ups-calculator.index')}} @else {{route('us-calculator.index')}} @endif" class="btn btn-md rounded px-5" style="background-color: #7367f0;color: #fff;">
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
    <input type="text" id="searchInput" class="form-control col-6 my-4" placeholder=" Search...">

    <table class="table  table-borderless p-0 table-responsive-md table-striped" id="kpi-report">
        <thead>
            <tr id="kpiHead">
                <th class="py-3 font-black">@lang('orders.Courier')</th>
                <th class="py-3 font-black">@lang('orders.Rating')</th>
                <th class="py-3 font-black">@lang('orders.Average Transit')</th>
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
                    <span class="color-gray standard-font">
                        {{$profitRate['name']}}
                    </span>
                </td>
                <td>
                    <div class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $profitRate['rating'])
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </td>
                <td>7-10 business days</td>
                <!-- <td class="category-tag"></td> -->
                @if(auth()->user()->hasRole('admin')) <td>{{$apiRates[$key]['rate']}} USD</td> @endif
                <!--<td></td> -->
                <td class="price-tag">
                    {{$profitRate['rate']}} USD
                </td>
                <td>
                    @if($userLoggedIn)
                        @if($selectedService!=$profitRate['service_sub_class'])  
                            <button id="btn-submit" wire:click="getLabel('{{ $profitRate['service_sub_class'] }}')" type="submit" class="btn btn-success btn-sm btn-submit"><i class="fas fa-print text-print mx-2"></i>Buy Label</button>
                         @endif
                     @endif
                </td>

            </tr> 
            @if($userLoggedIn && $selectedService==$profitRate['service_sub_class'])
                <tr>      
                    <td colspan="3"></td>       
                    <td colspan="3">
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
                        @error('selectedService')<div class="row mb-1 ml-4"> <div class="error text-danger">{{ $message }}</div> </div>@enderror 
                    </td>
                </tr>
            @endif
            @endforeach


        </tbody>
    </table>
</section>
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
    $('.btn-submit').click(function(){
        $('#loading').fadeIn(); 
    }); 
    window.addEventListener('fadeOutLoading', event => {
        $('#loading').fadeOut();
    })
</script>
@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">
<style>
    .animated-value {
    transition: opacity 0.3s ease-in-out; /* You can customize the transition effect */
    }

    .popup-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .popup-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        max-width: 400px;
    }

    .buttons {
        margin-top: 20px;
    }

    button {
        padding: 10px 20px;
        margin-right: 10px;
        cursor: pointer;
    }

    button:hover {
        background-color: #f0f0f0;
    }

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

    .standard-font {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .color-gray {
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
    <div id="popup-container" class="popup-container">
        <div class="popup-content">
            <p>
                <strong>
                    Amount included
                </strong>
                (DDP - Delivered Duty Paid)
                Sender of the package pays for <strong> import taxes and duties. Import tax and duty </strong> charges will be included in the <strong> Total Charge. </strong>
            </p>
            <p>
                If customs determines that the actual
                value of the goods in the package is
                higher than declared,<strong> import tax and duty charges </strong> will increase.
            </p>
            <p>

                <strong> Amount to be paid by receiver </strong>
                (DDU- Delivered Duty Unpaid)
                Receiver will have to pay indicated
                amount for <strong>import taxes and duties </strong>. In
                addition, a courier-specific handling fes
                may apply.
            </p>
            <p>
                The risk is that the receiver may reject
                the package if hs/shs is unhappy with
                the <strong>import taxes and duties </strong> charges.
            </p>

            <div class="buttons float-right">
                <button id="reject-btn" class="btn btn-danger">Reject</button>
                <button id="accept-btn" class="btn btn-success">Accept & Continue</button>
            </div>
        </div>
    </div>

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
    <div class="my-3">
        <label for="">Tax And Duty</label>
        
        <select class="form-control selectpicker show-tic col-4" wire:model="selectedTaxModality" placeholder="@lang('orders.order-details.Tax Modality')">
            <option value="ddu" {{ 'ddu' == old('tax_modality') ? 'selected' : '' }}>Apply DDU</option>
            <option value="ddp" {{ 'ddp' == old('tax_modality') ? 'selected' : '' }}>Apply DDP</option>
        </select>
    </div>
    <table class="table  table-borderless p-0 table-responsive-md table-striped" id="kpi-report">
        <thead>
            <tr id="kpiHead">
                <th class="py-3 font-black">@lang('orders.Courier')</th>
                <th class="py-3 font-black">@lang('orders.Rating')</th>
                <th class="py-3 font-black">@lang('orders.Average Transit')</th>
                @if(auth()->user()->hasRole('admin')) <th>@lang('orders.Actual Cost')</th> @endif
                <th  class="py-3 font-black">
                    <div wire:loading.remove class="animated-value">
                         @lang('orders.Total Cost') - ( {{ strtoupper($selectedTaxModality)}})
                    </div>
                </th>
                <th class="py-3 font-black">@lang('orders.actions.actions')</th>
            </tr>
            <tr>
                <th><input type="text" id="searchInput" class="form-control" placeholder=" Search..."></th>
                <td></td>
                <td></td>

                <th><input type="text" id="actualRate" class="form-control" placeholder=" Actual Rate"></th>
                <th><input type="text" id="totalRate" class="form-control" placeholder=" Rate"></th>
                <th></th>
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
                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$profitRate['rating']) <i class="fas fa-star"></i>
                            @else
                            <i class="far fa-star"></i>
                            @endif
                            @endfor
                    </div>
                </td>
                <td>7-10 business days </td>
                @if(auth()->user()->hasRole('admin')) <td class="actual-rate">{{$apiRates[$key]['rate']}} USD</td> @endif

                <!--<td></td> -->
                <td class="price-tag total-rate">
                    <div wire:loading.remove class="animated-value custom-tooltip-calculator">
                           {{ $this->calculateTotal($profitRate['service_sub_class'],$apiRates[$key]['rate']) }} USD
                       
                        <span>

                            <i class="fa fa-info"></i>
                            <div class="tooltip-text-calculator">
                                <p>
                                    <strong>
                                        Amount included
                                    </strong>
                                    (DDP - Delivered Duty Paid)
                                    Sender of the package pays for <strong> import taxes and duties. Import tax and duty </strong> charges will be included in the <strong> Total Charge. </strong>
                                </p>
                                <p>
                                    If customs determines that the actual
                                    value of the goods in the package is
                                    higher than declared,<strong> import tax and duty charges </strong> will increase.
                                </p>
                                <p>

                                    <strong> Amount to be paid by receiver </strong>
                                    (DDU- Delivered Duty Unpaid)
                                    Receiver will have to pay indicated
                                    amount for <strong>import taxes and duties </strong>. In
                                    addition, a courier-specific handling fes
                                    may apply.
                                </p>
                                <p>
                                    The risk is that the receiver may reject
                                    the package if hs/shs is unhappy with
                                    the <strong>import taxes and duties </strong> charges.
                                </p>
                            </div>
                        </span>
                    </div>
                </td>
                @if($isInternational)
                <td>
                    @if($userLoggedIn)
                     <button wire:click="openModel('{{ $profitRate['service_sub_class'] }}','{{ $apiRates[$key]['rate'] }}','order')" type="submit" class="btn btn-success btn-sm btn-submit"><i class="feather icon-shopping-cart mx-2"></i>Place Order</button>
                    @endif
                </td>
                @else
                <td>
                    @if($userLoggedIn)
                        @if($selectedService!=$profitRate['service_sub_class'])
                        <button wire:click="openModel('{{ $profitRate['service_sub_class'] }}','{{ $apiRates[$key]['rate'] }}','lable')" type="submit" class="btn btn-success btn-sm btn-submit"><i class="feather icon-shopping-cart mx-2"></i>Buy Label</button>
                        @endif
                    @endif
                </td>
                @endif

            </tr>
            @if($userLoggedIn && $selectedService==$profitRate['service_sub_class'])
            <tr>
                <td class="text-right" colspan="6">
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
                    @error('selectedService')<div class="row mb-1 ml-4">
                        <div class="error text-danger">{{ $message }}</div>
                    </div>@enderror
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
        $('#totalRate').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#kpi-report tbody tr').each(function() {
                var totalRate = $(this).find('.total-rate').text().toLowerCase();
                if (totalRate.indexOf(value) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $('#actualRate').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#kpi-report tbody tr').each(function() {
                var actualRate = $(this).find('.actual-rate').text().toLowerCase();
                if (actualRate.indexOf(value) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

    });

    document.addEventListener("DOMContentLoaded", function() {
        var popupContainer = document.getElementById("popup-container");
        var acceptBtn = document.getElementById("accept-btn");
        var rejectBtn = document.getElementById("reject-btn");
        var orderBtn = document.getElementById("order-btn");

        acceptBtn.addEventListener("click", function() {
            $('#loading').fadeIn();
            createOrder();
            popupContainer.style.display = "none";
        });

        rejectBtn.addEventListener("click", function() {
            // If the user rejects the agreement, handle it accordingly
            console.log("User rejected the agreement.");
            popupContainer.style.display = "none";
        });

        // Show the popup when the page loads

        // Function to create the order using Livewire
        function createOrder() {
            Livewire.emit('acceptedAndContinue');
        }
        popupContainer.style.display = "none";
    }); 
    window.addEventListener('fadeOutLoading', event => {
        $('#loading').fadeOut();
    })
    window.addEventListener('termAndConditionOpen', function(event) {
        var popupContainer = document.getElementById("popup-container");
        popupContainer.style.display = "flex";
    });
    Livewire.hook('afterDomUpdate', () => {
        gsap.from('.animated-value', { opacity: 0, y: -50, duration: 0.3, ease: "power2.out", stagger: 0.1 });
    });

</script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>

@endsection
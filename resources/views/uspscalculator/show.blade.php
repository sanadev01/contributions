@extends('layouts.app')
@section('content')
    <!-- Dashboard Analytics Start -->
    <section id="vue-calculator">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                Rate Calculated For USPS
                            </h2>
                        </div>

                        <div class="col-md-12">
                            <x-flash-message></x-flash-message>
                        </div>
                         
                        <div class="card-body">
                            @if ($shipping_rates != null)
                                <div class="text-center">
                                    @foreach ($shipping_rates as $shipping_rate) 
                                        <div class="card-body"><div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Service Name
                                        </div> <div class="border col-5 py-1">
                                            {{$shipping_rate['name']}}
                                        </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Weight
                                        </div> <div class="border col-5 py-1">
                                            @if($order->measurement_unit == 'kg/cm')
                                                {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                                            @else
                                                {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                                            @endif
                                        </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                            Cost
                                        </div> <div class="border col-5 py-1 text-danger h2">

                                            {{$shipping_rate['rate']}} USD
                                        
                                            <br>
                                        
                                        </div></div></div></div></div>
                                        <hr>
                                    @endforeach
                                </div>
                                @if ($userLoggedIn)
                                    @if(auth()->user()->usps == false) 
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-12">
                                                <h4 class="text-danger">USPS is not enabled for your account</h4>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mb-1 ml-4">
                                        <div class="controls col-12" id="usps_response">
                                        </div>
                                    </div>
                                    <form id="SubmitUSPSForm">
                                        @csrf
                                        <input type="hidden" name="order" value="{{ $order }}" id="order">
                                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-6">
                                                <label>@lang('orders.order-details.Select Shipping Service')<span class="text-danger"></span></label>
                                                <select name="shipping_service" id="shipping_service" class="form-control" required>
                                                    @foreach ($shipping_rates as $shipping_service)
                                                        <option {{ old('shipping_service') == $shipping_service['name'] ? 'selected' : '' }} value="{{ $shipping_service['name'] }}" data-cost="{{ $shipping_service['rate']}}">{{ $shipping_service['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="controls col-6" id="buy_label_div">
                                                <button id="btn-submit" type="button" class="btn btn-success btn-lg mt-4" @if(auth()->user()->usps == false) disabled @endif>
                                                    Buy Label
                                                </button>
                                            </div>
                                            <div class="controls col-6" id="print_label_div" style="display: none;">
                                                <a href="" type="button" class="btn btn-success btn-lg mt-4" id="print_label_btn">
                                                    Print Label
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @endif
                            <br>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                <a href="{{route('usps-calculator.index')}}" class="btn btn-primary btn-lg">
                                        Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin'))
                        <div class="card p-2">
                            <div class="card-header pb-0">
                                <h2 class="mb-2 text-center w-100">
                                    Rate Calculated For USPS (without Profit)
                                </h2>
                            </div>

                            <div class="col-md-12">
                                <x-flash-message></x-flash-message>
                            </div>

                            <div class="card-body">
                                @if ($usps_rates != null)
                                    <div class="text-center">
                                        @foreach ($usps_rates as $usps_rate) 
                                            <div class="card-body">
                                                <div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Service Name
                                            </div> <div class="border col-5 py-1">
                                                {{$usps_rate['name']}}
                                            </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Weight
                                            </div> <div class="border col-5 py-1">
                                                @if($order->measurement_unit == 'kg/cm')
                                                    {{$chargableWeight}} Kg ( {{$weightInOtherUnit}} lbs)
                                                @else
                                                    {{$chargableWeight}} lbs ( {{$weightInOtherUnit}} kg)
                                                @endif
                                            </div></div> <div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Cost
                                            </div> <div class="border col-5 py-1 text-danger h2">

                                                {{$usps_rate['rate']}} USD
                                            
                                                <br>
                                            
                                            </div></div></div></div></div>
                                            <hr>
                                        @endforeach
                                    </div>
                                @endif
                                <br>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                    <a href="{{route('usps-calculator.index')}}" class="btn btn-primary btn-lg">
                                            Go Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Analytics end -->
@endsection
@section('jquery')
    <script>
    $('#btn-submit').click(function(e){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        e.preventDefault();
        let service = $('#shipping_service option:selected').text();
        let usps_cost = $('#shipping_service option:selected').attr('data-cost');
        let order = $('#order').val();
        let user_id = $('#user_id').val();
        
        $.ajax({
            type:'POST',
            url:"{{ route('api.buy_usps_label') }}",
            data:{
                service:service, 
                usps_cost:usps_cost,  
                order:order,
                user_id:user_id
            },
            success:function(response){
                if(response.success == false)
                {
                    $('#usps_response').empty().append("<h4 style='color: red;'>"+response.message+"</h4>");
                }
                if(response.success == true)
                {
                    $('#buy_label_div').css('display', 'none');
                    $('#print_label_div').css('display', 'block');
                    $('#print_label_btn').attr("href", response.path);
                    $('#usps_response').empty().append("<h4 style='color: green;'>"+response.message+"</h4>");
                }
            },
            error: function(response) {
                console.log(response);
                $('#usps_response').empty().append("<h4 style='color: red;'>"+response.message+"</h4>");
            }
        });
    });
    </script>
@endsection
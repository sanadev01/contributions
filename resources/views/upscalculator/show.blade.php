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
                                Rate Calculated For UPS
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
                                    @if(!setting('ups', null, auth()->user()->id)) 
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-12">
                                                <h4 class="text-danger">UPS is not enabled for your account</h4>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mb-1 ml-4">
                                        <div class="controls col-12" id="ups_response">
                                        </div>
                                    </div>
                                    <form id="SubmitUPSForm">
                                        @csrf
                                        <input type="hidden" name="order" value="{{ $order }}" id="order">
                                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-6">
                                                <label>@lang('orders.order-details.Select Shipping Service')<span class="text-danger"></span></label>
                                                <select name="shipping_service" id="shipping_service" class="form-control" required>
                                                    @foreach ($shipping_rates as $shipping_service)
                                                        <option {{ old('shipping_service') == $shipping_service['name'] ? 'selected' : '' }} value="{{ $shipping_service['sub_class_code'] }}" data-cost="{{ $shipping_service['rate']}}">{{ $shipping_service['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="controls col-6" id="buy_label_div">
                                                <button id="btn-submit" type="button" class="btn btn-success btn-lg mt-4" @if(!setting('ups', null, auth()->user()->id)) disabled @endif>
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
                                <a href="{{route('ups-calculator.index')}}" class="btn btn-primary btn-lg">
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
                                    Rate Calculated For UPS (without Profit)
                                </h2>
                            </div>

                            <div class="col-md-12">
                                <x-flash-message></x-flash-message>
                            </div>

                            <div class="card-body">
                                @if ($ups_rates != null)
                                    <div class="text-center">
                                        @foreach ($ups_rates as $ups_rate)
                                            <div class="card-body">
                                                <div class="row justify-content-center mb-2 full-height align-items-center"><div class="col-10"><div class="row justify-content-center"><div class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                Service Name
                                            </div> <div class="border col-5 py-1">
                                                {{$ups_rate['name']}}
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

                                                {{$ups_rate['rate']}} USD
                                            
                                                <br>
                                            
                                            </div></div></div></div></div>
                                            <hr>
                                        @endforeach
                                    </div>
                                @endif
                                <br>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                    <a href="{{route('ups-calculator.index')}}" class="btn btn-primary btn-lg">
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
        let service = $('#shipping_service option:selected').val();
        let ups_cost = $('#shipping_service option:selected').attr('data-cost');
        let order = $('#order').val();
        let user_id = $('#user_id').val();
        $('#ups_response').empty().append("<h4 style='color: blue;'>Processing......</h4>");
        $('#btn-submit').prop('disabled', true);
        $('#btn-submit').html("Loading");

        $.ajax({
            type:'POST',
            url:"{{ route('api.buy_ups_label') }}",
            data:{
                service:service, 
                ups_cost:ups_cost,  
                order:order,
                user_id:user_id
            },
            success:function(response){
                if(response.success == false)
                {
                    $('#ups_response').empty().append("<h4 style='color: red;'>"+response.message+"</h4>");
                    $('#btn-submit').html("Failed");
                }
                if(response.success == true)
                {
                    $('#buy_label_div').css('display', 'none');
                    $('#print_label_div').css('display', 'block');
                    $('#print_label_btn').attr("href", response.path);
                    $('#ups_response').empty().append("<h4 style='color: green;'>"+response.message+"</h4>");
                    $('#btn-submit').html("Label Generated");
                }
            },
            error: function(response) {
                console.log(response);
                $('#ups_response').empty().append("<h4 style='color: red;'>"+response.message+"</h4>");
                $('#btn-submit').html("Failed");
            }
        });

    });
    </script>
@endsection
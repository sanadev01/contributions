@extends('layouts.master')
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('tax.Add Tax')</h4>
                        <a href="{{ route('admin.tax.index') }}" class="btn btn-primary">
                            @lang('tax.Back to List')
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if ($errors->count())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="form" action="{{ route('admin.tax.create') }}" method="GET">
                                <div class="row m-1">
                                    <div class="form-group col-sm-6 col-md-3">
                                        <div class="controls">
                                            <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                            <livewire:components.search-user selectedId="{{request('user_id')}}" />
                                            @error('pobox_number')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3">
                                        <div class="controls">
                                            <label>Tracking No.<span class="text-danger">*</span></label>
                                            <textarea type="text" placeholder="Please Enter Tracking Codes" rows="2" 
                                            class="form-control"
                                                name="trackingNumbers">{{ old('trackingNumbers',request('trackingNumbers')) }}</textarea>
                                            @error('trackingNumbers')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3">
                                        <button type="submit" class="btn btn-primary mt-5">Find</button>
                                    </div>
                                </div>
                            </form></br>
                            <form class="form" action="{{ route('admin.tax.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @if ($orders)
                                    @foreach ($orders as $key => $order)

                                    @if($loop->first)
                                    <div>
                                        <div class="row m-1 mb-2">
                                            <div class="col-md-3">
                                                <label><b> Herco (Buying) (R$)</b></label>
                                            </div>
                                            <div class="col-md-3">
                                                <label><b> Herco (Selling) (R$)</b></label>
                                            </div>
                                        </div>
                                        <div class="row m-1 mb-4 orders">
                                            <div class="col-md-3">
                                                <input type="number" class="form-control buyingBRRate" id="buyingBRRate"
                                                    min="0" name="buying_br" value="{{ old('buying_br') }}"
                                                    step="0.01" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control sellingBRRate" id="sellingBRRate"
                                                    min="0" name="selling_br" value="{{ old('selling_br') }}"
                                                    step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row m-1 mb-2">
                                        <div class="col-md-1">
                                            <label><b>@lang('tax.Warehouse No.')</b></label>
                                        </div>
                                        <div class="col-md-1">
                                            <label><b>@lang('tax.User Name')</b></label>
                                        </div>
                                        <div class="col-md-2">
                                            <label><b>@lang('tax.Tracking Code')</b></label>
                                        </div>
                                        <div class="col-md-2">
                                            <label><b>@lang('tax.Tax Payment')</b></label>
                                        </div>


                                        <div class="col-md-2">
                                            <label><b> Herco (Buying) (R$)</b></label>
                                        </div>
                                        <div class="col-md-2">
                                            <label><b> Herco (Selling) (R$)</b></label>
                                        </div>
                                        <div class="col-md-1">
                                            <label><b>@lang('tax.Profit') USD</b></label>
                                        </div>
                                        <div class="col-md-1">
                                            <label><b>@lang('Attachment')</b></label>
                                        </div>
                                    </div>
                                    @endif

                                        @if ($order->tax)
                                            <div class="row m-1 mt-3 orders">
                                                <div class="col-md-1">
                                                    <p class="text-danger">
                                                        {{ $order->warehouse_number }}
                                                    </p>
                                                </div>
                                                <div class="col-md-1">
                                                    <p class="text-danger">
                                                        {{ $order->user->name }}
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-danger">
                                                        {{ $order->corrios_tracking_code }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="text-danger h4">
                                                        @lang('tax.Order Paid')
                                                    </p>
                                                </div>
                                            </div>
                                            @error('deposit' . $order->id)
                                                <div class="row  mb-3 text-success ">
                                                    @lang('tax.Balance deposit')
                                                </div>
                                            @enderror
                                        @else
                                            <div class="row m-1 mt-3 orders">
                                                <div class="col-md-1">
                                                    <input type="hidden" class="form-control" name="user_id"
                                                        value="{{ $order->user_id }}">
                                                    <input type="hidden" class="form-control" name="order_id[]"
                                                        value="{{ $order->id }}">
                                                    <input type="text" class="form-control"
                                                        value="{{ $order->warehouse_number }}" readonly required>
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"
                                                        class="form-control
                                                        name="user_name[{{ $order->id }}]"
                                                        value="{{ $order->user->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control"
                                                        name="tracking_code[{{ $order->id }}]"
                                                        value="{{ $order->corrios_tracking_code }}" readonly required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control  taxPayment" min="1"
                                                        name="tax_payment[{{ $order->id }}]"
                                                        value="{{ old('tax_payment.' . $order->id) }}" step="0.01"
                                                        required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-control buyingUSD"
                                                        name="buying_usd[{{ $order->id }}]"
                                                        value="{{ old('buying_usd.' . $order->id) }}" step="0.01"
                                                        readonly required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input class="form-control sellingUSD"
                                                        name="selling_usd[{{ $order->id }}]"
                                                        value="{{ old('selling_usd.' . $order->id) }}" step="0.01"
                                                        readonly required>
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text" class="form-control profit"
                                                        name="profit[{{ $order->id }}]"
                                                        value="{{ old('profit.' . $order->id) }}" readonly required>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class="btn pr-0" href='javascript:void(0)'>
                                                        <button class="btn btn-success btn-md" type="button"><i
                                                                class="fa fa-upload"></i></button>
                                                        <input multiple type="file"
                                                            name="attachment[{{ $order->id }}][]"
                                                            style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;'
                                                            size="40"
                                                            onchange='$("#upload-file-info-{{ $order->id }}").html($(this).value());'>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row m-1 mt-3 orders">
                                                <div class="col-md-6">
                                                    <span class='float-right mr-5  label label-info'
                                                        id="upload-file-info-{{ $order->id }}"></span>
                                                </div>
                                            </div>
                                        @endif
                                        @if($loop->last) 
                                        <div class="row m-1 mt-3 orders h4"">
                                            <div class="col-md-3"> 
                                            </div> 
                                            <div class="col-md-1 h4"> 
                                                Total
                                            </div> 
                                            <div class="col-md-2" >
                                               R$<span id="taxPaymentTotal">0</span>
                                            </div>
                                            <div class="col-md-2">
                                               US$ <span id="buyingUSDTotal">0</span> 
                                            </div>
                                            <div class="col-md-2">
                                              US$ <span id="sellingUSDTotal">0</span> 
                                            </div>
                                            <div class="col-md-1">
                                              <span id="profitTotal">0</span> 
                                            </div>
                                            <div class="col-md-1"> 
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    <div class="row mt-4 mb-4">
                                        <div
                                            class="col-12 d-flex text-center flex-sm-row flex-column justify-content-end mt-1">
                                            <button type="submit"
                                                class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-4 waves-effect waves-light">
                                                @lang('tax.Pay')
                                            </button>
                                        </div>
                                    </div>
                        </div>
                        @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        $('body').on('change', '.orders input.taxPayment ', function() {
            let buyingBRRate = $('#buyingBRRate').val();
            let sellingBRRate = $('#sellingBRRate').val();
            let taxPayment = $(this).closest('.orders').find('.taxPayment').val(); 
            let buyingUSD = parseFloat(taxPayment) / parseFloat(buyingBRRate);
            let sellingUSD = parseFloat(taxPayment) / parseFloat(sellingBRRate); 
            let profit = parseFloat(sellingUSD) - parseFloat(buyingUSD);

            $(this).closest('.orders').find('.profit').val(
                isNaN(profit) ? 0 : (profit).toFixed(2)
            );
            $(this).closest('.orders').find('.sellingUSD').val(
                isNaN(sellingUSD) || !isFinite(sellingUSD) ? 0 : (sellingUSD).toFixed(2)
            );
            $(this).closest('.orders').find('.buyingUSD').val(
                isNaN(buyingUSD) || !isFinite(buyingUSD) ? 0 : (buyingUSD).toFixed(2)
            );
            calculateTotal()
        });

        $('body').on('change', 'input.buyingBRRate, input.sellingBRRate ', function() {
            let buyingBRRate = $('#buyingBRRate').val();
            let sellingBRRate = $('#sellingBRRate').val();
            var data = {!! json_encode($orders, JSON_HEX_TAG) !!};
            data.forEach(element => {
                let taxPayment = $(`input[name="tax_payment[${element.id}]"]`).val();

                let buyingUSD = parseFloat(taxPayment) / parseFloat(buyingBRRate);
                let sellingUSD = parseFloat(taxPayment) / parseFloat(sellingBRRate);
                let profit = parseFloat(sellingUSD) - parseFloat(buyingUSD);

                $(`input[name="selling_usd[${element.id}]"]`).val(parseFloat(sellingUSD).toFixed(2));
                $(`input[name="buying_usd[${element.id}]"]`).val(parseFloat(buyingUSD).toFixed(2));
                $(`input[name="profit[${element.id}]"]`).val(parseFloat(profit).toFixed(2));
            });
            calculateTotal()
        });

      function calculateTotal(){
            let buyingBRRate = $('#buyingBRRate').val();
            let sellingBRRate = $('#sellingBRRate').val();

            let taxPaymentTotal=0;
            let buyingUSDTotal=0;
            let sellingUSDTotal=0;
            var data = {!! json_encode($orders, JSON_HEX_TAG) !!};
            data.forEach(element => {
                let taxPayment = $(`input[name="tax_payment[${element.id}]"]`).val();
                  taxPaymentTotal = parseFloat(taxPaymentTotal) + parseFloat(taxPayment);

                  buyingUSDTotal = buyingUSDTotal +  parseFloat(taxPayment) / parseFloat(buyingBRRate);
                  sellingUSDTotal = sellingUSDTotal + parseFloat(taxPayment) / parseFloat(sellingBRRate);

            });
             let  profitTotal = parseFloat(sellingUSDTotal) - parseFloat(buyingUSDTotal);
            if(!isNaN(taxPaymentTotal))
                $("#taxPaymentTotal").text(parseFloat(taxPaymentTotal).toFixed(2));
            if(!isNaN(buyingUSDTotal)) 
                $("#buyingUSDTotal").text(parseFloat(buyingUSDTotal).toFixed(2));
            if(!isNaN(sellingUSDTotal))
                $("#sellingUSDTotal").text(parseFloat(sellingUSDTotal).toFixed(2)); 
            if(!isNaN(profitTotal))
            $("#profitTotal").text(parseFloat(profitTotal).toFixed(2));
        }
    });
</script>
@endsection

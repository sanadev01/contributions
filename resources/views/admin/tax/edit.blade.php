@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update Tax Transaction</h4>
                        <a href="{{ route('admin.tax.index') }}" class="pull-right btn btn-primary">@lang('role.Back to List') </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if( $errors->count() )
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('admin.tax.update',$tax->id) }}" method="post" class="orders" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden"  name="deposit_id" value="{{ $tax->deposit_id }}">
                                <input type="hidden"  name="user_id" value="{{ $tax->user_id }}">
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Tracking Code')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="track_id" value="{{ old('tracking_code', $tax->order->corrios_tracking_code) }}" placeholder="Tracking Code" readonly>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Tax Payment')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number"  min="1"  step="0.01" class="form-control taxPayment" name="tax_payment" value="{{ old('tax_payment', $tax->tax_payment) }}" placeholder="@lang('tax.Tax Payment')">
                                        <div class="help-block"></div>
                                    </div>
                                </div> 
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Herco Buying Rate')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" min="0"  step="0.01" class="form-control buyingBRRate" name="buying_br" value="{{ old('buying_br', $tax->buying_br) }}" placeholder="@lang('tax.Herco Buying Rate')" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Herco Selling Rate')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" min="0" step="0.01" class="form-control sellingBRRate" name="selling_br" value="{{ old('selling_br', $tax->selling_br) }}" placeholder="@lang('tax.Herco Selling Rate')" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Herco Buying USD') <span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" min="1" step="0.01" class="form-control buyingUSD" name="buying_usd" value="{{ old('buying_usd', $tax->buying_usd) }}" placeholder="@lang('tax.Herco Buying USD')" readonly required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Herco Selling USD')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" min="1" step="0.01" class="form-control sellingUSD" name="selling_usd" value="{{ old('selling_usd', $tax->selling_usd) }}" placeholder="@lang('tax.Herco Selling USD')" readonly required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Profit') USD<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" class="form-control profit" name="profit" value="{{ old('profit', ( $tax->selling_usd) -$tax->buying_usd) }}" placeholder="@lang('tax.Profit') USD" readonly required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Paid At<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="created_at" value="{{ old('created_at',   \Carbon\Carbon::parse($tax->created_at)->format('Y-m-d') ) }}"  placeholder="yyyy--mm-dd" placeholder="Created At" >
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Upload Receipt')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="file" class="form-control" name="attachment[]" multiple>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-7 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('tax.Update')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('tax.Reset')</button>
                                    </div>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function () { 
            
            $('body').on('change','input.taxPayment ,input.sellingBRRate ,  input.buyingBRRate ',function(){ 
                let sellingBRRate = $('input.sellingBRRate').val();
                let  buyingBRRate = $('input.buyingBRRate').val(); 
 
                let taxPayment = $('input.taxPayment').val();

                let buyingUSD = parseFloat(taxPayment) / parseFloat(buyingBRRate); 
                let sellingUSD = parseFloat(taxPayment) / parseFloat(sellingBRRate);
              
                let profit = parseFloat(sellingUSD) - parseFloat(buyingUSD);

                $(this).closest('.orders').find('.profit').val(
                    isNaN(profit) ? 0 : (profit).toFixed(2)
                );
                $(this).closest('.orders').find('.sellingUSD').val(
                    isNaN(sellingUSD)|| !isFinite(sellingUSD) ? 0 : (sellingUSD).toFixed(2)
                );
                $(this).closest('.orders').find('.buyingUSD').val(
                    isNaN(buyingUSD) || !isFinite(buyingUSD)? 0 : (buyingUSD).toFixed(2) 
                );

            });
        })
    </script>
@endsection

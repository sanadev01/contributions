@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update Adjustment</h4>
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
                            <form action="{{ route('admin.adjustment.update',$tax->id) }}" method="post" class="orders" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') 
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Adjustment')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" class="form-control taxPayment" name="adjustment" value="{{ old('adjustment', $tax->adjustment) }}" placeholder="@lang('tax.adjustment')">
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

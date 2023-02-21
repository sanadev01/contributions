@extends('layouts.master')
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('tax.Adjustment')</h4>
                        <a href="{{ route('admin.tax.index') }}" class="btn btn-primary">
                            @lang('tax.Back to List')
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body"> 
                            <form class="form" action="{{ route('admin.adjustment.store') }}" method="POST">
                                @csrf
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
                                            <label>  Ajustment.<span class="text-danger">*</span></label>
                                            <input type="number" placeholder="Adjustment" rows="2" step=".01"
                                           class="form-control"
                                                name="adjustment">{{ old('adjustment',request('adjustment')) }}</input>
                                            @error('adjustment')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3">
                                        <button type="submit" class="btn btn-primary mt-5">Save</button>
                                    </div>
                                </div>
                            </form></br>
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
            $('body').on('change', '.orders input.taxPayment ,input.buyingBRRate, input.sellingBRRate', function() {

                let buyingBRRate = $(this).closest('.orders').find('.buyingBRRate').val();
                let sellingBRRate = $(this).closest('.orders').find('.sellingBRRate').val(); 
                let taxPayment = $(this).closest('.orders').find('.taxPayment').val(); 
                console.log('buyingBRRate')
                console.log(buyingBRRate)
                let buyingUSD = parseFloat(taxPayment) / parseFloat(buyingBRRate); 
                let sellingUSD = parseFloat(taxPayment) / parseFloat(sellingBRRate);
                console.log('buyingUSD')
                console.log(buyingUSD)
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

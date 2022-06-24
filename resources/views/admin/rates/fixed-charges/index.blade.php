@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @lang('shipping-rates.Fixed Charges')
                            </h4>
                            <hr>
                        </div>
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <form action="{{ route('admin.rates.fixed-charges.store') }}" method="POST">
                            @csrf
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('shipping-rates.consolidation Charges')<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" required class="form-control" name="consolidation_charges" value="{{ old('consolidation_charges',setting('consolidation_charges')) }}" placeholder="@lang('shipping-rates.consolidation Charges')">
                                    @error('consolidation_charges')
                                    <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        @lang('shippingservice.Save Changes')
                                    </button>
                                    <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('shippingservice.Reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

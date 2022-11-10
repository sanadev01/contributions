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
                            <form action="{{ route('admin.tax.update',$tax->id) }}" method="post" enctype="multipart/form-data">
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
                                        <input type="text" class="form-control" name="tax_payment" value="{{ old('tax_payment', $tax->tax_payment) }}" placeholder="Enter Tax Payment">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('Exchange Rste')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="convert_rate" value="{{ old('convert_rate', $tax->convert_rate) }}" placeholder="Enter Tax Payment">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Buying Rates USD<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="buying_usd" value="{{ old('tax_1', $tax->buying_usd) }}" placeholder="Enter Seller Tax" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Selling Rates USD<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="selling_usd" value="{{ old('tax_1_br', $tax->selling_usd) }}" placeholder="Enter Seller Tax" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Tax Herco')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="buying_br" value="{{ old('tax_2', $tax->buying_br) }}" placeholder="Enter Tax By Herco" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Tax Customer')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="selling_br" value="{{ old('tax_2_br', $tax->selling_br) }}" placeholder="Enter Tax By Herco" required>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('tax.Upload Receipt')<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <input type="file" class="form-control" name="attachment">
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

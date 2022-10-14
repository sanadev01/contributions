@extends('layouts.master')
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('taxservice.Add Tax')</h4>
                        <a href="{{ route('admin.tax.index') }}" class="btn btn-primary">
                            @lang('taxservice.Back to List')
                        </a>
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
                            <form class="form" action="{{ route('admin.tax.create') }}" method="GET">
                                <div class="row m-1">
                                    <div class="form-group col-sm-6 col-md-3">
                                        <div class="controls">
                                            <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                            <livewire:components.search-user />
                                            @error('pobox_number')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3">
                                        <div class="controls">
                                            <label>Tracking No.<span class="text-danger">*</span></label>
                                            <textarea type="text" placeholder="Please Enter Tracking Codes" rows="2" class="form-control" name="trackingNumbers"></textarea>
                                            @error('trackingNumbers')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3">
                                        <button type="submit" class="btn btn-primary mt-5">Find</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-3 mt-4">
                                        <a class='btn' href='javascript:;'>
                                            <button class="btn btn-success btn-md" type="button">Upload Files <i class="fa fa-upload"></i></button>
                                            <input type="file" multiple name="attachment[]" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' size="40"  onchange='$("#upload-file-info").html($(this).val());'>

                                        </a>
                                    </div>
                                </div>
                            </form></br>
                            <form class="form" action="{{ route('admin.tax.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                    @if($orders)
                                        <div class="row m-1 mb-2">
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.Order ID')</b></label>
                                            </div>
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.User Name')</b></label>
                                            </div>
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.Tracking Code')</b></label>
                                            </div>
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.Tax Customer')</b></label>
                                            </div>
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.Tax Herco')</b></label>
                                            </div>
                                            <div class="col-md-2">
                                                <label><b>@lang('taxservice.Profit')</b></label>
                                            </div>
                                        </div>
                                        @foreach($orders as $order)
                                            <div class="row m-1 mb-3">
                                                <div class="col-md-2">
                                                    <input type="hidden" class="form-control" name="user_id" value="{{ $order->user_id }}">
                                                    <input type="hidden" class="form-control" name="order_id[]" value="{{ $order->id }}">
                                                    <input type="text" class="form-control"  value="{{ $order->warehouse_number }}" readonly required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="user_name[]" value="{{ $order->user->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="tracking_code[]" value="{{ $order->corrios_tracking_code }}" readonly required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="tax1" name="tax_1[]" value="" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="tax2" name="tax_2[]" value="" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="profit" name="profit[]" value=""  readonly required>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="row mt-4 mb-4">
                                            <div class="col-12 d-flex text-center flex-sm-row flex-column justify-content-end mt-1">
                                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-4 waves-effect waves-light">
                                                    @lang('taxservice.Pay')
                                                </button>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('body').on('keyup','#tax2',function(){
            let tax1 = $('#tax1').val();
            let tax2 = $('#tax2').val();
            let profit = parseFloat(tax1) - parseFloat(tax2);
            $('#profit').val( isNaN(profit) ? 0 : (profit).toFixed(2) );
        });
    })
</script>
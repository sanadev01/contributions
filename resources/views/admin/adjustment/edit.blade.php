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
                                        <input type="number" step="0.01" class="form-control taxPayment" name="adjustment" value="{{ old('adjustment', $tax->adjustment) }}" placeholder="Please enter adjustment price">
                                        <div class="help-block"></div>
                                    </div>
                                </div> 
                                 


                                <div class="controls row mb-1 align-items-center my-3">
                                    <label class="col-md-3 text-md-right">Reasone<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                        <textarea type="text" placeholder="Our customer service  applied wrong ex change rates to the customer" 
                                        value="" rows="2" 
                                        class="form-control"
                                            name="reasone">{{ old('reasone','Our customer service  applied wrong ex change rates to the customer') }}</textarea>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
 
                                <div class="controls row mb-1 align-items-center my-3">
                                    <label class="col-md-3 text-md-right">Attachment<span class="text-danger">*</span></label>
                                    <div class="col-md-4">
                                                                      <a class="btn pr-0" href='javascript:void(0)'>
                                        <button class="btn btn-success btn-md" type="button"><i
                                                class="fa fa-upload"></i></button>
                                        <input multiple type="file" name="attachment[]"
                                        style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;'
                                          size="40"
                                            onchange='$("#upload-file-info").html($(this).value());'>
                                    </a>
                                    </div>
      
                                </div>                                    
                                <div class="controls row mb-1 align-items-center my-3">  
                                    <div class="col-md-6">
                                         <span class='float-right mr-5  label label-info' id="upload-file-info"></span>
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
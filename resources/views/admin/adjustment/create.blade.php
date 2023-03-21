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
                            <form class="form" action="{{ route('admin.adjustment.store') }}" method="POST"
                            enctype="multipart/form-data">
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
                                            <input type="number" placeholder="Please enter adjustment price" rows="2" step=".01"
                                           class="form-control"
                                                name="adjustment">{{ old('adjustment',request('adjustment')) }}</input>
                                            @error('adjustment')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6 col-md-3">
                                        <div class="controls">
                                            <label>  Reason<span class="text-danger">*</span></label>
                                            
                                            <textarea type="text" placeholder="Please enter reason" 
                                            value="" rows="2" 
                                            class="form-control"
                                                name="reason">{{ old('reason') }}</textarea>
                                            @error('reason')
                                                <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-1 mt-5">
                                        <a class="btn pr-0" href='javascript:void(0)'>
                                            <button class="btn btn-success btn-md" type="button"><i
                                                    class="fa fa-upload"></i></button>
                                            <input multiple type="file" name="attachment[]"
                                            style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;'
                                              size="40"
                                                onchange='$("#upload-file-info").html($(this).value());'>
                                        </a>
                                    </div>                                    
                                    <div class="row m-1 mt-3 orders">  
                                        <div class="col-md-6">
                                             <span class='float-right mr-5  label label-info' id="upload-file-info"></span>
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
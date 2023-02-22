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
                                            <input type="number" placeholder="Please enter adjustment price" rows="2" step=".01"
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
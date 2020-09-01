@extends('layouts.master')
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Create Shipping Service</h4>
                        <a href="{{ route('admin.shipping-services.index') }}" class="btn btn-primary">
                            Back to List
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
                            <form action="{{ route('admin.shipping-services.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Name<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" required class="form-control" name="name" value="{{ old('name') }}" placeholder="Name of Service">
                                        @error('name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Max length allowed<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="max_length_allowed" placeholder="Max length allowed" value="{{ old('max_length_allowed') }}">
                                        @error('max_length_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Max width allowed<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="max_width_allowed" placeholder="Max width allowed" value="{{ old('max_width_allowed') }}">
                                        @error('max_width_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Min length allowed<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="min_length_allowed" placeholder="Min length allowed" value="{{ old('min_length_allowed') }}">
                                        @error('min_length_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Min width allowed<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="min_width_allowed" placeholder="Min width allowed" value="{{ old('min_width_allowed') }}">
                                        @error('min_width_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Max sum of all sides<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="max_sum_of_all_sides" placeholder="Max sum of all sides" value="{{ old('max_sum_of_all_sides') }}">
                                        @error('max_sum_of_all_sides')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Contains battery charges<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="contains_battery_charges" required value="{{ old('contains_battery_charges') }}" placeholder="contains_battery_charges" >
                                            <option value="">Contains battery charges</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('contains_battery_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Contains perfume charges<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="contains_perfume_charges" required value="{{ old('contains_perfume_charges') }}" placeholder="Contains perfume charges" >
                                            <option value="">Contains perfume charges</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('contains_perfume_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Contains flammable liquid charges<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="contains_flammable_liquid_charges" required value="{{ old('contains_flammable_liquid_charges') }}" placeholder="Contains flammable liquid charges" >
                                            <option value="">Contains flammable liquid charges</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('contains_flammable_liquid_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            Save Changes
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Reset</button>
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

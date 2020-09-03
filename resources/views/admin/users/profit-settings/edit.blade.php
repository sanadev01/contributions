@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form">Profit and Commision Settings</h4>
            <a class="btn btn-primary" href="{{ route('admin.users.index',['search'=>$user->name]) }}">
                Back to List
            </a>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <form class="form" action="{{ route('admin.users.profit-and-comission.store',$user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Select Profit Package</label>
                                <select name="package" class="form-control">
                                    <option value="">Choose Package</option>
                                    @foreach ($packages as $package)
                                        <option value="{{$package->id}}" {{ $package->id == $user->profit_id ? 'selected':'' }}>{{$package->name}}</option>
                                    @endforeach
                                </select>
                                @error('package')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <label for="">Is Referral</label>
                                <input type="checkbox" class="form-control w-50 h-50" name="is_referral" {{ $user->is_referral ? 'checked': '' }}>
                            </div>
                            <div class="col-md-3">
                                <label for="">Comission Type</label>
                                <select name="comission_type" class="form-control">
                                    <option value="flat" {{ $user->comission_type== 'flat' ? 'selected' : '' }}>Flat Value</option>
                                    <option value="percent" {{ $user->comission_type== 'percent' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Comission Value</label>
                                <input type="number" class="form-control" name="comission_value" value="{{ old('comission_value',$user->comission_value) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions pl-5 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

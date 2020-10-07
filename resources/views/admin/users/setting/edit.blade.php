@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Settings</h4>
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
                            <form action="{{ route('admin.users.setting.store', $user->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                 
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('user.Package')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="package_id" class="form-control">
                                            <option value="" selected disabled hidden>@lang('user.Select Package')</option>
                                            @isset($packages)
                                                @foreach ($packages as $package)
                                                    <option @if( $user->package_id == $package->id ) selected @endif value="{{ $package->id }}">{{ $package->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('user.Role')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="role_id" class="form-control">
                                            @isset($roles)
                                                @foreach ($roles as $role)
                                                    <option @if( $user->role_id == $role->id ) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <div class="help-block"></div>
                                    </div>  
                                </div>

                                <h3>Api Settings</h3>
                                <hr>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Enable API<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="checkbox" name="api_enabled" id="api_enabled" @if( $user->api_enabled == 1 ) checked @endif> 
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <livewire:token-generator :user_id="$user->id" />

                                <h3>Leve Settings</h3>
                                <hr>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Market Place Name<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="market_place_name" class="form-control" value="{{ $user->market_place_name }}"> 
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <h3>Profile Settings</h3>
                                <hr>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Email<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="user_email" class="form-control" value="{{ $user->email }}"> 
                                        @error('user_email')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Update Password<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="password" name="password" class="form-control" value=""> 
                                        @error('password')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
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

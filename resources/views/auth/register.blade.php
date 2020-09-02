@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header h3">{{ __('auth.register.Register') }}</div>
                <hr>
                <div class="card-body" id="registration-form">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="h6">{{ __("auth.register.Account Type") }}</label>
                                    <div class="d-flex mt-1">
                                        <div class="vs-checkbox-con vs-checkbox-primary d-flex mr-3">
                                            <input type="radio" class="mr-1" value="individual" name="account_type" {{ old('account_type') == 'individual' ? 'checked': '' }}>
                                            <span class="vs-checkbox vs-checkbox-lg">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">{{ __("auth.register.Individual") }}</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary d-flex mr-3">
                                            <input type="radio" class="mr-1" value="business" name="account_type" {{ old('account_type') == 'individual' ? 'checked': '' }}>
                                            <span class="vs-checkbox vs-checkbox-lg">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">{{ __("auth.register.Business") }}</span>
                                        </div>
                                    </div>
                                </div>
                                @error('account_type')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-6 mb-1">
                                <div class="form-group mb-0">
                                    <label for="name" id="register_first_name" class="col-form-label text-md-right">{{ __('auth.register.First Name') }} <span class="text-danger h4">*</span></label>
                                    <label for="name" id="register_company_name" style="display: none" class="col-form-label text-md-right">{{ __('auth.register.Company Name') }} <span class="text-danger h4">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-1" >
                                <div class="form-group mb-0">
                                    <label for="last_name" class="col-form-label text-md-right">{{ __('auth.register.Last Name') }} <span class="text-danger h4">*</span></label>
                                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="email" class="col-form-label text-md-right">{{ __('auth.register.Email Address') }} <span class="text-danger h4">*</span></label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-1">
                                <div class="form-group mb-0">
                                    <label for="phone" class="col-form-label text-md-right">{{ __('auth.register.Phone No') }} <span class="text-danger h4">* (Formato Internacional)</span></label>
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="+55123456789" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="col-form-label text-md-right">{{ __('auth.register.Password') }} <span class="text-danger h4">*</span></label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password-confirm" class="col-form-label text-md-right">{{ __('auth.register.Confirm Password') }} <span class="text-danger h4">*</span></label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mb-0">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth.register.Register') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-info">
                                    {{ __('auth.nav.Login') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer">
                    <span class="text-danger h4">*</span><span class="text-danger h4">*</span><span class="text-danger h4">*</span>
                    @lang('auth.register.Fields marked with')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jquery')
<script>
    $(document).ready(function(){
        $('input[name="account_type"]').on('click', function(){
            let val = $(this).val()

            if(val == 'individual'){
                $('#register_first_name').css('display', 'inline')
                $('#register_company_name').css('display', 'none')
            }else{
                $('#register_first_name').css('display', 'none')
                $('#register_company_name').css('display', 'inline')
            }
        })
    })
</script>
@endsection


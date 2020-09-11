@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="mb-0">
                                @lang('address.Edit Address')
                            </h4>
                            <p>@lang('address.corios-message')</p>
                        </div>
                        <a href="{{ route('admin.addresses.index') }}" class="pull-right btn btn-primary"> @lang('address.Back to List') </a>
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
                            <form action="{{ route('admin.addresses.update',$address) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div>
                                    <div class="row mt-1">
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Type') <span class="text-danger">*</span></label>
                                                <select class="form-control" name="account_type" id="accountType" required placeholder="Account Type">
                                                    <option value="">@lang('address.Type')</option>
                                                    <option @if ($address->account_type == 'individual') selected @endif value="individual">Individual</option>
                                                    <option @if ($address->account_type == 'business') selected @endif value="business">Business</option>
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.First Name') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{$address->first_name}}" name="first_name" required placeholder="First Name">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Last Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"value="{{$address->last_name}}"   name="last_name" required placeholder="Last Name">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Email') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{$address->email}}"  name="email" required placeholder="Email">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Phone') <span class="text-danger">*(Formato internacional)</span></label>
                                                <input type="text" class="form-control" value="{{$address->phone}}"  name="phone" required placeholder="+55123456789">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Address') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{$address->address}}"  name="address" required placeholder="Address"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Address')2</label>
                                                <input type="text" class="form-control" value="{{$address->address2}}"  placeholder=""  name="address2">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.street-no')</label>
                                                <input type="text" class="form-control" placeholder="" value="{{$address->street_no}}"  name="street_no">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>@lang('address.Country') <span class="text-danger">*</span></label>
                                                    <select name="country_id" class="form-control">
                                                        @foreach ($countries as $country)
                                                            <option @if ($address->country_id == $country->id) selected @endif value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.UF') <span class="text-danger">*</span></label>
                                                <select name="state_id" class="form-control">
                                                    <option value="">Select @lang('address.UF')</option>
                                                    @foreach ($states as $state)
                                                        <option @if ($address->state_id == $state->id) selected @endif value="{{ $state->id }}">{{ $state->code }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.City') <span class="text-danger">*</span></label>
                                                <input type="text" name="city" value="{{$address->city}}"  class="form-control"  required placeholder="City"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Zip Code')</label>
                                                <input type="text" name="zipcode" value="{{$address->zipcode}}"  required class="form-control" placeholder="Zip Code"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label style="display: none" id="cpf_label_id" >@lang('address.CPF') <span class="text-danger">* (Brazil Only)</span> </label>
                                                <label id="cnpj_label_id" >@lang('address.CNPJ') <span class="text-danger">* (Brazil Only)</span> </label>
                                                <input type="text" name="tax_id" id="tax_id" value="{{$address->tax_id}}" required class="form-control" placeholder="CNPJ"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('address.Save')
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

@section('js')
    <script>
        $(document).ready(function(){

            let val = $('#accountType').val();
            if(val == 'individual'){
                $('#cpf_label_id').css('display', 'block')
                $('#cnpj_label_id').css('display', 'none')
                $('#tax_id').attr('placeholder', 'CPF')
            }else{
                $('#cpf_label_id').css('display', 'none')
                $('#cnpj_label_id').css('display', 'block')
                $('#tax_id').attr('placeholder', 'CNPJ')
            }

            $('#accountType').on('change', function(){
                let val = $(this).val();
                if(val == 'individual'){
                    $('#cpf_label_id').css('display', 'block')
                    $('#cnpj_label_id').css('display', 'none')
                    $('#tax_id').attr('placeholder', 'CPF')
                }else{
                    $('#cpf_label_id').css('display', 'none')
                    $('#cnpj_label_id').css('display', 'block')
                    $('#tax_id').attr('placeholder', 'CNPJ')
                }
            })
        })
    </script>
@endsection
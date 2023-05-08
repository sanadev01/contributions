@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
        @section('title', __('shipping-rates.BPS Rates'))
        <a href="{{ route('admin.rates.shipping-rates.index') }}" class="btn btn-primary pull-right">
            @lang('shipping-rates.Return to List')
        </a>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body paddinglr">
            <form class="form" action="{{ route('admin.rates.accrual-rates.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <h4 class="form-section">@lang('shipping-rates.Import BPS Leve Rates Excel')</h4>
                        </div>
                    </div>
                    <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>@lang('shipping-rates.Country') <span class="text-danger">*</span></label>
                                        <select name="country_id" id="country" required class="form-control">
                                            <option value="" selected>@lang('shipping-rates.Select Country')</option>
                                            <option value="30">Brazil</option>
                                            <option value="46">Chile</option>
                                            <option value="50">Colombia</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>@lang('shipping-rates.Shipping Service') <span class="text-danger">*</span></label>
                                        <select name="service_id" id="service" required class="form-control">
                                            <option value="" selected>Select Service</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_STANDARD}}">Standard</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_EXPRESS}}">Express</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_MINI}}">Mini</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_SRP}}">SRP</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_SRM}}">SRM</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_AJ_Standard}}">AJ Standard</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_AJ_EXPRESS}}">AJ Express</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_GePS}}">Global eParcel Prime</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_GePS_EFormat}}">Global eParcel Untracked Packet</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Prime5}}">Prime5</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_MILE_EXPRESS}}">Mile Express</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_PostNL}}">PostNL</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_COLOMBIA_URBANO}}">Colombia URBANO</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_COLOMBIA_NACIONAL}}">Colombia NACIONAL</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_COLOMBIA_TRAYETOS}}">Colombia TRAYETOS</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Registered}}">Post Plus Registered</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_EMS}}">Post Plus EMS</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Parcel_Post}}">Parcel Post</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Prime}}">Post Plus Prime</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Premium}}">PrimeRIO</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Prime5RIO}}">Prime5RIO</option>
                                            <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_GDE}}">GDE</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="csv_file"
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        required>
                                    @error('csv_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <label class="custom-file-label" for="inputGroupFile01">@lang('shipping-rates.Select Excel File to Upload')<span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="alert alert-warning">
                                <ol>
                                    <li>@lang('shipping-rates.* Upload only Excel files')</li>
                                    <li>@lang('shipping-rates.* Files larger than 15Mb are not allowed')</li>
                                    <li>@lang('shipping-rates.* Download and fill in the data in the sample file below to avoid errors')</li>
                                    {{-- <li class="mt-2">Download Sample File <a href="{{ asset('uploads/accrual.xlsx') }}" class="btn btn-success btn-sm">@lang('shipping-rates.Download')</a></li> --}}
                                    <li class="mt-2">@lang('shipping-rates.* Download the sample File')
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-success dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    @lang('shipping-rates.Download')
                                                </button>
                                                <div class="dropdown-menu overlap-menu"
                                                    aria-labelledby="dropdownMenuLink">
                                                    <a href="{{ asset('uploads/accrual.xlsx') }}"
                                                        class="dropdown-item">@lang('shipping-rates.Download')</a>
                                                    <a href="{{ asset('uploads/chile-accural.xlsx') }}"
                                                        class="dropdown-item">@lang('shipping-rates.Chile Rates')</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions pl-5">
                    <a href="{{ route('admin.rates.shipping-rates.index') }}" class="btn btn-warning mr-1 ml-3">
                        <i class="ft-x"></i> @lang('shipping-rates.Cancel')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('shipping-rates.Import')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function(){ 
        var countryChile = {!! $countryChile !!};

        $('#country').change(function () {
            let selected = $('#country').val();
            
            if(selected == countryChile) {
                $('#service').children("option[value=" + '33162' + "]").hide();
                $('#service').children("option[value=" + '33170' + "]").hide();
                $('#service').children("option[value=" + '33197' + "]").hide();
                $('#service').children("option[value=" + '33164' + "]").hide();
                $('#service').children("option[value=" + '33172' + "]").hide();

                $('#service').children("option[value=" + '28' + "]").show();
                $('#service').children("option[value=" + '32' + "]").show();
            } else {
                $('#service').children("option[value=" + '28' + "]").hide();
                $('#service').children("option[value=" + '32' + "]").hide();

                $('#service').children("option[value=" + '33162' + "]").show();
                $('#service').children("option[value=" + '33170' + "]").show();
                $('#service').children("option[value=" + '33197' + "]").show();
                $('#service').children("option[value=" + '33164' + "]").show();
                $('#service').children("option[value=" + '33172' + "]").show();
                
            }
        });

    })
</script>
@endsection

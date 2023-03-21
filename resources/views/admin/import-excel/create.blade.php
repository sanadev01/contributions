@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form"></h4>
            <a href="{{ route('admin.import.import-excel.index') }}" class="btn btn-primary pull-right">
                @lang('Return to List')
            </a>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="card-body paddinglr">
            @if ($errors->count())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="form" action="{{ route('admin.import.import-excel.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            {{-- <h4 class="form-section">@lang('orders.import-excel.Import Orders via Excel Sheet')</h4> --}}
                        @section('title', __('orders.import-excel.Import Orders via Excel Sheet'))
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="projectinput1">@lang('orders.import-excel.Excel File Name')</label>
                            <input type="text" class="form-control" name="excel_name" placeholder="Enter file name"
                                required>
                            @error('excel_name')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="projectinput1">@lang('orders.import-excel.Excel File Format')</label>
                            <select class="form-control" name="format">
                                <option value=""> @lang('orders.import-excel.Select Format')</option>
                                <option value="homedelivery"> @lang('orders.import-excel.Homedelivery Format')</option>
                                <option value="shopify"> @lang('orders.import-excel.Shopify Format')</option>
                                <option value="xml"> @lang('orders.import-excel.Xml Format')</option>
                            </select>
                            @error('format')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('shipping-rates.Shipping Service') <span class="text-danger">*</span></label>
                                <select name="service_id" id="service" required class="form-control">
                                    <option value="" selected>Select Service</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_STANDARD}}">Standard</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_EXPRESS}}">Express</option>
                                    {{-- <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_MINI}}">Mini</option> --}}
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_SRP}}">SRP</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_SRM}}">SRM</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_GePS}}">GePS</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Prime5}}">Prime5</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Registered}}">Registered</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_EMS}}">EMS</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Prime}}">Post Plus Prime</option>
                                    <option value="{{App\Services\Correios\Models\Package::SERVICE_CLASS_Post_Plus_Premium}}">Post Plus Premium</option>
                                </select>
                                @error('service_id')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectinput1">@lang('orders.import-excel.Select Excel File to Upload')</label>
                                <input type="file" class="form-control" name="excel_file"
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/xml"
                                    required>
                                @error('excel_file')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                {{-- 
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="controls mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="excel_file"
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/xml"
                                        required>
                                    @error('excel_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <label class="custom-file-label" for="inputGroupFile01">@lang('orders.import-excel.Select Excel File to Upload')<span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> --}}
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="alert" style="background: #ffcaca !important;">
                            <ol>
                                {{-- <li>@lang('orders.import-excel.Upload only Excel files')</li>
                                    <li>@lang('orders.import-excel.Files larger than 15Mb are not allowed')</li>
                                    <li class="mt-2">@lang('orders.import-excel.Download the sample') Homedelivery <a href="{{ asset('uploads/order-import.xlsx') }}" class="btn btn-success btn-sm">@lang('orders.import-excel.Download')</a></li>
                                    <li class="mt-2">@lang('orders.import-excel.Download the sample') Shopify <a href="{{ asset('uploads/shopify-format.xlsx') }}" class="btn btn-success btn-sm">@lang('orders.import-excel.Download')</a></li>
                                    <li class="mt-2">@lang('orders.import-excel.Download the sample') Xml <a href="{{ asset('uploads/xml-format.xml') }}" class="btn btn-success btn-sm">@lang('orders.import-excel.Download')</a></li> --}}

                                <li>@lang('orders.import-excel.Files Tempelate')</li>
                                <li>@lang('orders.import-excel.Download and fill in the data in the sample file below to avoid errors')</li>
                                <li>@lang('orders.import-excel.Choose the format')</li>
                            </ol>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-left">
                                        <strong>Homedelivery Template</strong>
                                    </div>
                                    <div class="col-12 d-flex justify-content-left">
                                        <ol>
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#homedeliveryModal">
                                                    @lang('orders.import-excel.instructions homedelivery')

                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ asset('uploads/order-import.xlsx') }}">
                                                    @lang('orders.import-excel.Download the Homedelivery')
                                                </a>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-left">
                                        <strong>Shopify Template</strong>
                                    </div>
                                    <div class="col-12 d-flex justify-content-left">
                                        <ol>
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#shopifyModal">
                                                    @lang('orders.import-excel.instructions shopify')

                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ asset('uploads/shopify-format.xlsx') }}">
                                                    @lang('orders.import-excel.Download the Shopify')
                                                </a>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-left">
                                        <strong>XML Template</strong>
                                    </div>
                                    <div class="col-12 d-flex justify-content-left">
                                        <ol>
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#xmlModal">
                                                    @lang('orders.import-excel.instructions xml')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ asset('uploads/xml-format.xml') }}" target="_blank">
                                                    @lang('orders.import-excel.Download the XML')
                                                </a>
                                            </li>
                                        </ol>

                                    </div>
                                </div>
                            </div>
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
<div class="modal fade bd-example-modal-lg" id="homedeliveryModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Homedelivery Sheet Instructions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <div class="modal-body">
                <strong>merchant (Required)</strong> @lang('orders.import-excel.Use to add customer merchant Name')
                <br><strong>carrier (Required)</strong> @lang('orders.import-excel.Use to add customer carrier')
                <br><strong>tracking id (Required)</strong> @lang('orders.import-excel.Use to add customer tracking id ')
                <br><strong>customer refrence# (Optional)</strong> @lang('orders.import-excel.Use to add customer refrence')
                <br><strong>weight (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>length (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>width (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>height (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>measurment unit (Required)</strong> @lang('orders.import-excel.Use to add kg/cm or lbs/in')
                <br><strong>sender first_name (Required)</strong> @lang('orders.import-excel.Use to add sender First Name')
                <br><strong>sender last name (Required)</strong> @lang('orders.import-excel.Use to add sender Last Name')
                <br><strong>sender_email (Optional)</strong> @lang('orders.import-excel.Use to add sender Email')
                <br><strong>sender_phone (Optional)</strong> @lang('orders.import-excel.Use to add sender phone')
                <br><strong>Sender_city (required when creating order for Chile)</strong>@lang('orders.import-excel.Use to add sender City')
                <br><strong>Sender_address (required when creating order for Chile)</strong> @lang('orders.import-excel.Use to add sender Address')
                <br><strong>recipient first_name (Required)</strong> @lang('orders.import-excel.Use to add recipient first name')
                <br><strong>recipient last_name (Optional)</strong> @lang('orders.import-excel.Use to add recipient last name')
                <br><strong>recipient_email (Optional)</strong> @lang('orders.import-excel.Use to add recipient email')
                <br><strong>recipient_phone (Required)</strong> @lang('orders.import-excel.Use to add recipient phone')
                <br><strong>recipient_address (Required)</strong> @lang('orders.import-excel.Use to add recipient address')
                <br><strong>recipient_address_2 (Optional)</strong> @lang('orders.import-excel.Use to add recipient address 2')
                <br><strong>recipient_house_number (Required)</strong> @lang('orders.import-excel.Use to add recipient house/Street number')
                <br><strong>recipient_zipcode (Required)</strong> @lang('orders.import-excel.Use to add Zipcode')
                <br><strong>recipient_region (required when creating order for Chile)</strong> @lang('orders.import-excel.Use to add Region')
                <br><strong>recipient_city (Required/ When creating Order for Chile City/Commune must be of Chile
                    )</strong> @lang('orders.import-excel.Use to add City')
                <br><strong>recipient_state_abbrivation (Required)</strong> @lang('orders.import-excel.Use to add State abbrivation (SE etc)')
                <br><strong>recipient_country_code_iso (Required)</strong> @lang('orders.import-excel.Use to add country code iso (BR)')
                <br><strong>recepient_tax_id (Required)</strong> @lang('orders.import-excel.Use to add customer tax id')
                <br><strong>Freight To Custom (Required)</strong> @lang('orders.import-excel.Use to add Freight Rate')
                <br><strong>product quantity (Required)</strong> @lang('orders.import-excel.Use to add Product quantity')
                <br><strong>value (Required)</strong> @lang('orders.import-excel.Use to add product Price')
                <br><strong>product name (Required)</strong> @lang('orders.import-excel.Use to add Product name ')
                <br><strong>NCM (Required)</strong> @lang('orders.import-excel.Use to add NCM/Sh Code')
                <br><strong>perfume (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
                <br><strong>battery (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="shopifyModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Shopify Sheet Instructions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>merchant (Required)</strong> @lang('orders.import-excel.Use to add customer merchant Name')
                <br><strong>carrier (Required)</strong> @lang('orders.import-excel.Use to add customer carrier')
                <br><strong>tracking id (Required)</strong> @lang('orders.import-excel.Use to add customer tracking id ')
                <br><strong>customer refrence# (Optional)</strong> @lang('orders.import-excel.Use to add customer refrence')
                <br><strong>weight (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>length (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>width (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>height (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>measurment unit (Required)</strong> @lang('orders.import-excel.Use to add kg/cm or lbs/in')
                <br><strong>sender first_name (Required)</strong> @lang('orders.import-excel.Use to add sender First Name')
                <br><strong>sender last name (Required)</strong> @lang('orders.import-excel.Use to add sender Last Name')
                <br><strong>sender_email (Optional)</strong> @lang('orders.import-excel.Use to add sender Email')
                <br><strong>sender_phone (Optional)</strong> @lang('orders.import-excel.Use to add sender phone')
                <br><strong>Sender_city (required when creating order for Chile)</strong>@lang('orders.import-excel.Use to add sender City')
                <br><strong>Sender_address (required when creating order for Chile)</strong>
                <br><strong>recipient first_name (Required)</strong> @lang('orders.import-excel.Use to add recipient first name')
                <br><strong>recipient last_name (Optional)</strong> @lang('orders.import-excel.Use to add recipient last name')
                <br><strong>recipient_email (Optional)</strong> @lang('orders.import-excel.Use to add recipient email')
                <br><strong>recipient_phone (Required)</strong> @lang('orders.import-excel.Use to add recipient phone')
                <br><strong>recipient_address (Required)</strong> @lang('orders.import-excel.Use to add recipient address')
                <br><strong>recipient_address_2 (Optional)</strong> @lang('orders.import-excel.Use to add recipient address 2')
                <br><strong>recipient_house_number (Required)</strong> @lang('orders.import-excel.Use to add recipient house/Street number')
                <br><strong>recipient_zipcode (Required)</strong> @lang('orders.import-excel.Use to add Zipcode')
                <br><strong>recipient_region (required when creating order for Chile)</strong> @lang('orders.import-excel.Use to add Region')
                <br><strong>recipient_city (Required/ When creating Order for Chile City/Commune must be of Chile
                    )</strong> @lang('orders.import-excel.Use to add City')
                <br><strong>recipient_state_abbrivation (Required)</strong> @lang('orders.import-excel.Use to add State abbrivation (SE etc)')
                <br><strong>recipient_country_code_iso (Required)</strong> @lang('orders.import-excel.Use to add country code iso (Brazil)')
                <br><strong>recepient_tax_id (Required)</strong> @lang('orders.import-excel.Use to add customer tax id')
                <br><strong>Freight To Custom (Required)</strong> @lang('orders.import-excel.Use to add Freight Rate')
                <br><strong>product quantity (Required)</strong> @lang('orders.import-excel.Use to add Product quantity')
                <br><strong>value (Required)</strong> @lang('orders.import-excel.Use to add product Price')
                <br><strong>product name (Required)</strong> @lang('orders.import-excel.Use to add Product name ')
                <br><strong>NCM (Required)</strong> @lang('orders.import-excel.Use to add NCM/Sh Code')
                <br><strong>perfume (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
                <br><strong>battery (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="xmlModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">XML Instructions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>Merchant (Required)</strong>@lang('orders.import-excel.Use to add customer merchant Name')
                <br><strong>Carrier (Required)</strong>@lang('orders.import-excel.Use to add customer carrier')
                <br><strong>TrackingId (Required)</strong> @lang('orders.import-excel.Use to add customer tracking id ')
                <br><strong>CustomerRefrence refrence# (Optional)</strong>@lang('orders.import-excel.Use to add customer refrence')
                <br><strong>Weight (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>Length (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>Width (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>Height (Required)</strong> @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>MeasurmentUnit (Required)</strong> @lang('orders.import-excel.Use to add kg/cm or lbs/in')
                <br><strong>SenderFirstName (Required)</strong> @lang('orders.import-excel.Use to add sender First Name')
                <br><strong>SenderLastName (Required)</strong> @lang('orders.import-excel.Use to add sender Last Name')
                <br><strong>SenderPhone (Optional)</strong> @lang('orders.import-excel.Use to add sender phone')
                <br><strong>SenderEmail (Optional)</strong> @lang('orders.import-excel.Use to add sender Email')
                <br><strong>Sender_city (required when creating order for Chile)</strong>@lang('orders.import-excel.Use to add sender City')
                <br><strong>Sender_address (required when creating order for Chile)</strong>
                <br><strong>RecipientFirstName (Required)</strong> @lang('orders.import-excel.Use to add recipient first name')
                <br><strong>RecipientLastName (Required)</strong> @lang('orders.import-excel.Use to add recipient last name')
                <br><strong>RecipientEmail (Optional)</strong> @lang('orders.import-excel.Use to add recipient email')
                <br><strong>RecipientPhone (Optional)</strong> @lang('orders.import-excel.Use to add recipient phone')
                <br><strong>RecipientAddress (Required)</strong> @lang('orders.import-excel.Use to add recipient address')
                <br><strong>RecipientAddress2 (Required)</strong> @lang('orders.import-excel.Use to add recipient address 2')
                <br><strong>RecipientHouseNo (Optional)</strong> @lang('orders.import-excel.Use to add recipient house/Street number')
                <br><strong>RecipientZipcode (Required)</strong> @lang('orders.import-excel.Use to add Zipcode')
                <br><strong>recipient_region (required when creating order for Chile)</strong> @lang('orders.import-excel.Use to add Region')
                <br><strong>recipient_city (Required/ When creating Order for Chile City/Commune must be of Chile
                    )</strong> @lang('orders.import-excel.Use to add City')
                <br><strong>RecipientStateAbbrivation (Required)</strong> @lang('orders.import-excel.Use to add State abbrivation (SE etc)')
                <br><strong>RecipientCountryCodeIso (Required)</strong> @lang('orders.import-excel.Use to add country code iso (BR)')
                <br><strong>RecipientTaxId (Required)</strong> @lang('orders.import-excel.Use to add customer tax id')
                <br><strong>FreightToCustom (Required)</strong> @lang('orders.import-excel.Use to add Freight Rate')
                <br><strong>ProductQuantity (Required)</strong> @lang('orders.import-excel.Use to add Product quantity')
                <br><strong>ProductValue (Required)</strong> @lang('orders.import-excel.Use to add product Price')
                <br><strong>ProductDescription (Required)</strong> @lang('orders.import-excel.Use to add Product name ')
                <br><strong>NCM (Required)</strong> @lang('orders.import-excel.Use to add NCM/Sh Code')
                <br><strong>Perfume (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
                <br><strong>Battery (Optional)</strong> @lang('orders.import-excel.Use to add Yes Or No')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
@endsection

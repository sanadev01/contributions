@extends('layouts.master')

@section('page') 
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form"></h4>
            <a href="{{ route('admin.inventory.product.index') }}" class="btn btn-primary pull-right">
                @lang('Return to List')
            </a>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
    
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
            <form class="form" action="{{ route('admin.inventory.product-import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <h4 class="form-section">@lang('orders.import-excel.Import Orders via Excel Sheet')</h4>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectinput1">@lang('orders.import-excel.Excel File Name')</label>
                                <input type="text" class="form-control" name="excel_name" placeholder="Enter file name" required>
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
                                <label for="projectinput1">@lang('orders.import-excel.Select Excel File to Upload')</label>
                                <input type="file" class="form-control" name="excel_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/xml" required>
                                @error('excel_file')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="alert" style="background: #ffcaca !important;">
                                <ol>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions pl-5">
                    <a href="{{ route('admin.inventory.product.index') }}" class="btn btn-warning mr-1 ml-3">
                        <i class="ft-x"></i> @lang('shipping-rates.Cancel')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('shipping-rates.Import')
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="homedeliveryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Homedelivery Sheet Instructions</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <strong>merchant (Required)</strong>                        @lang('orders.import-excel.Use to add customer merchant Name')
                <br><strong>carrier (Required)</strong>                     @lang('orders.import-excel.Use to add customer carrier')
                <br><strong>tracking id (Required)</strong>                 @lang('orders.import-excel.Use to add customer tracking id ')
                <br><strong>customer refrence# (Optional)</strong>          @lang('orders.import-excel.Use to add customer refrence')
                <br><strong>weight (Required)</strong>                      @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>length (Required)</strong>                      @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>width (Required)</strong>                       @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>height (Required)</strong>                      @lang('orders.import-excel.Use to add greater then 0')
                <br><strong>measurment unit (Required)</strong>             @lang('orders.import-excel.Use to add kg/cm or lbs/in')
                <br><strong>sender first_name (Required)</strong>           @lang('orders.import-excel.Use to add sender First Name')
                <br><strong>sender last name (Required)</strong>            @lang('orders.import-excel.Use to add sender Last Name')
                <br><strong>sender_email (Optional)</strong>                @lang('orders.import-excel.Use to add sender Email')
                <br><strong>sender_phone (Optional)</strong>                @lang('orders.import-excel.Use to add sender phone')
                <br><strong>Sender_city (required when creating order for Chile)</strong>@lang('orders.import-excel.Use to add sender City')
                <br><strong>Sender_address (required when creating order for Chile)</strong>             @lang('orders.import-excel.Use to add sender Address')
                <br><strong>recipient first_name (Required)</strong>        @lang('orders.import-excel.Use to add recipient first name')
                <br><strong>recipient last_name (Optional)</strong>         @lang('orders.import-excel.Use to add recipient last name')
                <br><strong>recipient_email (Optional)</strong>             @lang('orders.import-excel.Use to add recipient email')
                <br><strong>recipient_phone (Required)</strong>             @lang('orders.import-excel.Use to add recipient phone')
                <br><strong>recipient_address (Required)</strong>           @lang('orders.import-excel.Use to add recipient address')
                <br><strong>recipient_address_2 (Optional)</strong>         @lang('orders.import-excel.Use to add recipient address 2')
                <br><strong>recipient_house_number (Required)</strong>      @lang('orders.import-excel.Use to add recipient house/Street number')
                <br><strong>recipient_zipcode (Required)</strong>           @lang('orders.import-excel.Use to add Zipcode')
                <br><strong>recipient_region (required when creating order for Chile)</strong>           @lang('orders.import-excel.Use to add Region')
                <br><strong>recipient_city (Required/ When creating Order for Chile City/Commune must be of Chile )</strong> @lang('orders.import-excel.Use to add City')
                <br><strong>recipient_state_abbrivation (Required)</strong> @lang('orders.import-excel.Use to add State abbrivation (SE etc)')
                <br><strong>recipient_country_code_iso (Required)</strong>  @lang('orders.import-excel.Use to add country code iso (BR)')
                <br><strong>recepient_tax_id (Required)</strong>            @lang('orders.import-excel.Use to add customer tax id')
                <br><strong>Freight To Custom (Required)</strong>           @lang('orders.import-excel.Use to add Freight Rate')
                <br><strong>product quantity (Required)</strong>            @lang('orders.import-excel.Use to add Product quantity')
                <br><strong>value (Required)</strong>                       @lang('orders.import-excel.Use to add product Price')
                <br><strong>product name (Required)</strong>                @lang('orders.import-excel.Use to add Product name ')
                <br><strong>NCM (Required)</strong>                         @lang('orders.import-excel.Use to add NCM/Sh Code')
                <br><strong>perfume (Optional)</strong>                     @lang('orders.import-excel.Use to add Yes Or No')
                <br><strong>battery (Optional)</strong>                     @lang('orders.import-excel.Use to add Yes Or No')
            </div> 				
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
@endsection

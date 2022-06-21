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
                    @admin
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                <livewire:components.search-user />
                                @error('pobox_number')
                                <div class="help-block text-danger"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endadmin
                    
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
                                                    <a href="{{ asset('uploads/product-import.xlsx') }}">
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
                <div class="container">
                    <strong>sh_code (Required)</strong> @lang('orders.import-excel.Use to add Harmonized Code')
                    <br><strong>name (Required)</strong> @lang('orders.import-excel.Use to add Product Name')
                    <br><strong>order (Required)</strong> @lang('orders.import-excel.Use to add Order Number')
                    <br><strong>price (Required)</strong> @lang('orders.import-excel.Use to add Price Per Item')
                    <br><strong>category (Required)</strong> @lang('orders.import-excel.Use to add Category')
                    <br><strong>sku (Required)</strong> @lang('orders.import-excel.Use to add SKU')
                    <br><strong>status (Required)</strong> @lang('orders.import-excel.Use to add Status')
                    <br><strong>description (Required)</strong> @lang('orders.import-excel.Use to add Description')
                    <br><strong>quantity (Required)</strong> @lang('orders.import-excel.Use to add Quantity')
                    <br><strong>brand (Required)</strong> @lang('orders.import-excel.Use to add Brand')
                    <br><strong>manufacturer (Required)</strong> @lang('orders.import-excel.Use to add Manufacturer')
                    <br><strong>barcode (Required)</strong> @lang('orders.import-excel.Use to add BarCode')
                    <br><strong>item (Required)</strong> @lang('orders.import-excel.Use to add Item Number')
                    <br><strong>lot (Required)</strong> @lang('orders.import-excel.Use to add lot Number')
                    <br><strong>unit (Required)</strong>@lang('orders.import-excel.Use to add Unit')
                    <br><strong>case (Required)</strong> @lang('orders.import-excel.Use to add Case')
                    {{-- <br><strong>inventory_value (Required)</strong> @lang('orders.import-excel.Use to add Inventory Value') --}}
                    <br><strong>min_quantity (Required)</strong> @lang('orders.import-excel.Use to add Minimum Quantity')
                    <br><strong>max_quantity (Required)</strong> @lang('orders.import-excel.Use to add Maximum Quantity')
                    <br><strong>discontinued (Required)</strong> @lang('orders.import-excel.Use to add Items Discontinued')
                    <br><strong>store_day (Required)</strong> @lang('orders.import-excel.Use to add Store Days')
                    <br><strong>location (Optional)</strong> @lang('orders.import-excel.Use to add Warehouse Location')
                </div>    
            </div> 				
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
@endsection

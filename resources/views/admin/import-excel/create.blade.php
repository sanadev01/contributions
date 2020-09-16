@extends('layouts.master')

@section('page') 
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form"></h4>
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
            <div class="card-body">
                <form class="form" action="{{ route('admin.import-excel.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <label for="projectinput1">@lang('orders.import-excel.Select Excel File to Upload')</label>
                                    <input type="file" class="form-control" name="excel_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
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
                                <div class="alert alert-warning">
                                    <ol>
                                        <li>@lang('orders.import-excel.Upload only Excel files')</li>
                                        <li>@lang('orders.import-excel.Files larger than 15Mb are not allowed')</li>
                                        <li>@lang('orders.import-excel.Download and fill in the data in the sample file below to avoid errors')</li>
                                        <li class="mt-2">@lang('orders.import-excel.Download the sample') <a href="{{ asset('uploads/order-import.xlsx') }}" class="btn btn-success btn-sm">@lang('orders.import-excel.Download')</a></li>
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

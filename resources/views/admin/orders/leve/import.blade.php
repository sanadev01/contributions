@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
        @section('title', __('orders.orders'))
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary pull-right">
            @lang('orders.leve.Return to List')
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
            <form class="form" action="{{ route('admin.leve-order-import.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <h4 class="form-section">@lang('orders.leve.Import Orders')</h4>
                        </div>
                    </div>

                    <div class="row justify-content-center">

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectinput1">@lang('orders.leve.Select Excel File to Upload')</label>
                                <input type="file" class="form-control" name="excel_file"
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    required>
                                @error('excel_file')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Upload</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="inputGroupFile01"
                                        name="excel_file"
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        required>
                                    @error('excel_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <label class="custom-file-label" for="inputGroupFile01">@lang('orders.leve.Select Excel File to Upload')<span
                                            class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="alert alert-warning">
                                <ol>
                                    <li>@lang('orders.leve.* Upload only Excel files')</li>
                                    <li>@lang('orders.leve.* Files larger than 15Mb are not allowed')</li>
                                    {{-- <li>@lang('orders.leve.* Download and fill in the data in the sample file below to avoid errors')</li> --}}
                                    {{-- <li class="mt-2">@lang('orders.leve.* Download the sample for bps rates') <a href="{{ asset('uploads/bps/hd-leve.xlsx') }}" class="btn btn-success btn-sm">@lang('orders.leve.Download')</a></li> --}}
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions pl-5">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-warning mr-1 ml-3">
                        <i class="ft-x"></i> @lang('orders.leve.Cancel')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('orders.leve.Import')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

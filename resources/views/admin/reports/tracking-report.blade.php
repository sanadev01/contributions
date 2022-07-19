@extends('layouts.master')
@section('css')
    <style>
        .picker__holder {
            bottom: unset !important;
        }
    </style>
@endsection
@section('page')
    <section>
        <div class="row h-100">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                    @section('title', __('Tracking Report'))
                </div>
                <div class="card-content">
                    <div class="card-body p-5">
                        <form action="{{ route('admin.reports.order-trackings.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Start Date<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control datepicker"
                                        value="{{ old('start_date') }}" name="start_date" required>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">End Date<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control datepicker"
                                        value="{{ old('end_date') }}" name="end_date" required>
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-11 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit"
                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        @lang('Download')
                                    </button>
                                    <button type="reset"
                                        class="btn btn-outline-secondary waves-effect waves-light">@lang('setting.Reset')</button>
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

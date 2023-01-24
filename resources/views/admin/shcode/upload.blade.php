@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                    @section('title', __('Upload ShCode'))
                    <a href="{{ route('admin.shcode.index') }}" class="pull-right btn btn-primary">Back to List</a>
                </div>
                <div class="card-content">
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
                        <form action="{{ route('admin.shcode-export.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="controls row mb-1 align-items-center d-flex justify-content-center">
                                <div class="controls">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Upload</span>
                                        </div>
                                        <div class="custom-file col-12">
                                            <input type="file" class="custom-file-input" name="file"
                                                value="{{ old('file') }}" placeholder="Select file of SH Code"
                                                required>
                                            <label class="custom-file-label" for="inputGroupFile01">File Upload<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="row justify-content-center mt-5">
                                <div class="col-md-10">
                                    <div class="alert" style="background: #ffcaca !important;">
                                        <ol>
                                            <li>@lang('shcode.Files Template')</li>
                                            <li>@lang('shcode.Download and fill')</li>
                                            <li>
                                                @lang('shcode.Choose the format')
                                                <a href="{{ asset('uploads/shcode.xlsx') }}">
                                                    @lang('shcode.Download')
                                                </a>
                                            </li>
                                        </ol>

                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit"
                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        @lang('shipping-rates.Import')
                                    </button>
                                    <button type="reset"
                                        class="btn btn-outline-warning waves-effect waves-light">@lang('role.Reset')</button>
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

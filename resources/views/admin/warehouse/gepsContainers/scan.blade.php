@extends('layouts.master')

@section('page')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('warehouse.containers.Packages Inside Container')
                        </h4>
                        <div>
                            <button type="button" class="btn btn-success" id="gepsUpload"> Upload Bulk </button>
                            <a href="{{ route('warehouse.geps_containers.index') }}" class="btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                            <a href="{{ route('warehouse.geps_container.packages.create',$container) }}" class="btn btn-success"> <i class="fa fa-arrow-down"></i> Download </a>
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:geps-container.packages :id="$container->id" :editMode="$editMode" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--GePS Bulk Trackings Upload ModalL-->
    <div class="modal fade" id="uploadModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Upload Bulk Trackings for Global eParcel</b></h5>
                </div>
                <form class="form" action="{{ route('warehouse.upload-bulk-trackings', $container) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body"><br>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projectinput1">@lang('shipping-rates.Select Excel File to Upload')</label>
                                    <input type="file" class="form-control" name="csv_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    @error('csv_file')
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
                                        <li>@lang('shipping-rates.* Upload only Excel files')</li>
                                        <li>@lang('shipping-rates.* Files larger than 15Mb are not allowed')</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="uspsAccept">Import Trackings</button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $("#gepsUpload").click(function(){
            $("#uploadModal").modal('show');
        });
        $("#scan").focus();
    });
    window.addEventListener('scan-focus', event => {
        $("#scan").focus();
    });
</script>
@endsection
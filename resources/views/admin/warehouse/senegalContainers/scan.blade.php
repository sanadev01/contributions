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
                            <a href="{{ route('warehouse.hd-senegal-containers.index') }}" class="btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:senegal-container.packages :id="$container->id" :editMode="$editMode">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $("#scan").focus();
    });
    window.addEventListener('scan-focus', event => {
        $("#scan").focus();
    });
</script>
@endsection
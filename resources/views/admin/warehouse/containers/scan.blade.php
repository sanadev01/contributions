@extends('layouts.master')

@section('page')
<section id="vue-scanner">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        @lang('warehouse.containers.Packages Inside Container')
                        <span class="badge">({{ $container->service_subclass_name}})</span>
                       
                    </h4>
                    <div>
                        <a href="{{ route('warehouse.containers.index') }}" class="btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                        <a href="{{ route('warehouse.containers.packages.create',$container) }}" class="btn btn-success"> <i class="fa fa-arrow-down"></i> Download </a>
                    </div>
                </div>
                <div class="card-content card-body">
                    <div class="mt-1">
                        <scanner-table :container='@json($container)' :edit-mode="{{$container->is_registered ? 'false':'true'}}" :orders-collection='@json($container->getOrdersCollections())' />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script src="{{ mix('js/pages/scanner.js') }}"></script>
@endpush
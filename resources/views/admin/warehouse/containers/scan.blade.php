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
                        <a href="{{ route('warehouse.containers.index') }}" class="pull-right btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <scanner-table :container='@json($container)' :orders-collection='@json($container->getOrdersCollections())'/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="{{ asset('js/pages/scanner.js') }}"></script>
@endpush
@extends('layouts.master')

@section('page')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                            @section('title',__('warehouse.containers.Packages Inside Container'))
                        <div>
                            <a href="{{ route('warehouse.sinerlog_containers.index') }}" class="btn btn-primary">
                                @lang('warehouse.containers.List Containers') </a>
                            <a href="{{ route('warehouse.sinerlog_container.packages.create', $sinerlog_container) }}"
                                class="btn btn-success"> <i class="fa fa-arrow-down"></i> Download </a>
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <scanner-table :container='@json($sinerlog_container)'
                                :edit-mode="{{ $sinerlog_container->isRegistered() ? 'false' : 'true' }}"
                                :orders-collection='@json($sinerlog_container->getOrdersCollections())' />
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

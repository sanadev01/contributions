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
                            <a href="{{ route('warehouse.swedenpost_containers.index') }}" class="btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                            <a href="{{ route('warehouse.swedenpost_container.packages.create',$container) }}" class="btn btn-success"> <i class="fa fa-arrow-down"></i> Download </a>
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:sweden-post-container.package  :id="$container->id" :edit-mode="$editMode"/> 

                            {{-- <livewire:swedenpost-container.packages :container="$container" :edit-mode="$editMode" :ordersCollection="$ordersCollection"> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="{{ asset('js/pages/scanner.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#scan").focus();
        });
        window.addEventListener('scan-focus', event => {
            $("#scan").focus();
        });
    </script>
@endpush
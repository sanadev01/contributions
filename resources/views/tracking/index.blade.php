@extends('layouts.app')

@section('content')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Track Your Packages
                        </h4>
                        <div>
                            {{-- <a href="{{ route('warehouse.containers.index') }}" class="btn btn-primary"> @lang('warehouse.containers.List Containers') </a>
                            <a href="{{ route('warehouse.containers.packages.create',$container) }}" class="btn btn-success"> <i class="fa fa-arrow-down"></i> Download </a> --}}
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:tracking.search-package>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    
@endsection
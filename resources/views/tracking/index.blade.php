@extends('layouts.app')

@section('content')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    @section('title', __('Track Your Packages'))
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

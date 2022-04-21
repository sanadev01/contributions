@extends('layouts.master')

@section('page')
<section id="app">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Barcode Scanner</h4>
                </div>
                <div class="card-content card-body">
                    <div class="mt-1">
                        <example-component></example-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('js')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush
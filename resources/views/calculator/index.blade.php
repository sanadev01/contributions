@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
<style>
    .number-input {
        margin-top: 3px;
        padding: 15px;
        font-size: 16px;
        width: 100%;
        border-radius: 3px;
        border: 1px solid #dcdcdc;
    }
    .breadcrumb-bg {
        background-color: #f8f8f8;
    }

    .card-bg {
        color: #373C3F;
        border: 1px solid #ffffff;
        background-color: #ffffff;
    }

    .btn-blue {
        background-color: #1174b7;
        color: white;
    }
</style>
@endsection
@section('page')
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<section>
    <div class="row mt-4">
        <div class="col-12 mx-2">
            <div class="ml-3">
                <dl>
                    <dt class="font-weight-bold dt">Calculator </dt>
                </dl>
            </div>
        </div>
    </div>
</section>
<!-- Dashboard Analytics Start -->
<section id="vue-calculator">
    <div class="mx-3">
        <div class="row mx-3">
                <livewire:calculator.correios-calculation>
        </div>
    </div>
</section>


<!-- Dashboard Analytics end -->
@endsection
@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('layouts.states-ajax')
@endsection
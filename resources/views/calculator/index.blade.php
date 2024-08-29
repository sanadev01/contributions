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
     
    .btn-purple {
      font-weight: bold;
      color: #fff; /* Text color */
      background-color: #7367f0; /* Purple color */
      border-color: #7367f0; /* Purple color */
    }
    .btn-purple:hover {  
      background-color: #a06dff; /* Darker shade of purple on hover */
      border-color: #a06dff; /* Darker shade of purple on hover */
    }
</style>
@endsection
@section('page')
<section>
    <nav>
        <ol class="breadcrumb breadcrumb-bg">
            <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="/calculator">Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">Correios</li>
        </ol>
    </nav>
</section>
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
        <div class="col-12">
            <dl>
                <dt class="font-weight-bold dt">Calculator</dt>
            </dl>
        </div>
    </div>
</section>
<!-- Dashboard Analytics Start -->
<section id="vue-calculator">
    <div class="mx-3">
        <div class="row">
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
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
    .standard-font{
         font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .color-gray{
        color: #6c757d;
    }
    .btn-purple {
      font-weight: bold;
      color: #fff;  
      background-color: #7367f0;  
      border-color: #7367f0;  
    }
    .btn-purple:hover {  
      background-color: #a06dff;  
      border-color: #a06dff;  
    }
</style>
@endsection
@section('page')
<section>
    <nav>
        <ol class="breadcrumb breadcrumb-bg">
            <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="/calculator">Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">WorldWide Calculator</li>
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
        <div class="col-12 mx-2">
            <div class="ml-3">
                <dl>
                    <dt class="font-weight-bold dt">Shipping Calculator</dt>
                </dl>
            </div>
        </div>
    </div>
</section>
<!-- Dashboard Analytics Start -->
<section id="vue-calculator">
    <div class="mx-3">
        <div class="row justify-content-center">
            <div class="col-12">
            <livewire:calculator.us-calculation cc='US'>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('uscalculator.script')
@include('uscalculator.searchAddress')

@endsection
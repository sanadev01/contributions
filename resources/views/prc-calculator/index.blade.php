@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">
<style>
    .vertical-rectangle {
        background-color: #1074B6;
        width: 3.1px;
        height: 16.8px;
        gap: 0px;
        opacity: 0px;
    }
    .form-group:hover input {
      border-left: 5px solid #1074B6;; 
      background-color: #F2FAFF;
    }
    .form-control{
        background-color: #F8F8F8;

    }
    .form-group:hover label {
      color: #1074B6;
    }
</style>
@endsection
@section('page')
<livewire:prc-calculator />
@endsection
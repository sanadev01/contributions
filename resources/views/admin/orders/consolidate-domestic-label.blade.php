@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endsection
@section('page')
    <livewire:order.consolidate-domestic-label-form :orders='$orders' :states='$states' :errors='$errors' :totalWeight='$totalWeight'/>
@endsection
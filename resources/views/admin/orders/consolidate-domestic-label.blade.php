@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endsection
@section('page')
    {{-- @livewire('order.consolidate-domestic-label-form', ['consolidatedOrder' => $consolidatedOrder, 'orders' => $orders, 'states' => $states, 'usShippingServices' => $usShippingServices, 'errors' => $errors]) --}}
    <livewire:order.consolidate-domestic-label-form :consolidatedOrder='$consolidatedOrder' :orders='$orders' :states='$states' :usShippingServices='$usShippingServices' :errors='$errors'/>
@endsection
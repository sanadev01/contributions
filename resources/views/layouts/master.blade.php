<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
{{--    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">--}}
{{--    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">--}}
{{--    <meta name="author" content="PIXINVENT">--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.css')
    @yield('css')
    <script>
        window.locale= "{{ app()->getLocale() }}";
    </script>
    <style>
        .modal{
            background-color: #2e2e2e9e;
        }
        .picker__holder{
            bottom: 100% !important;
        }
        .alert-warning {
            background: rgba(231, 0, 0, 0.44) !important;
            color: #000000 !important;
        }
        @media print
        {    
            .no-print, .no-print *
            {
                display: none !important;
            }
            *{
                -webkit-print-color-adjust: exact;
            }
        }

    </style>
    {{-- <livewire:styles> --}}
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<x-master-layout>

    <!-- BEGIN: Header-->
    <x-nav-bar></x-nav-bar>

    <x-user-menu></x-user-menu>

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <div class="content-header row">
                    <div class="col-md-12">
                        <x-flash-message></x-flash-message>
                    </div>
                </div>
                @yield('page')
            </div>
        </div>
    </div>
    <!-- END: Content-->
    {{-- <livewire:modal-popup /> --}}
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    
    @include('layouts.footer')
    @include('layouts.js')
    @yield('js')
    {{-- <livewire:scripts> --}}

    @yield('lvjs')

    <div class="position-fixed w-100 h-100 justify-content-center align-items-center" id="loading" style="z-index: 100000;top:0;right0; background-color:#ffffff75;display:flex;">
        <img src="{{ asset('images/loading.gif') }}" class="h-25" alt="">
    </div>
    <script>
        function confirmDelete(msg){
            return confirm(msg ?? 'Are you Sure to Delete');
        }
        $(window).on('load',function(){
            $('#loading').fadeOut();
        })
        $(window).on('beforeunload',function(){
            $('#loading').fadeIn();
            setTimeout(function(){
                $('#loading').fadeOut();
            },10000)
        })
    </script>
</x-master-layout>
<!-- END: Body-->

</html>

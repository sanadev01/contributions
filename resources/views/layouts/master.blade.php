<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
{{--    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">--}}
{{--    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">--}}
{{--    <meta name="author" content="PIXINVENT">--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla&display=swap" rel="stylesheet">
    {{-- <link href='https://fonts.googleapis.com/css?family=Karla' rel='stylesheet'> --}}
    @include('layouts.css')
    @yield('css')
    <script>
        window.locale= "{{ app()->getLocale() }}";
    </script>
    <livewire:styles>
    <script src="https://kit.fontawesome.com/8ea855d2d1.js" crossorigin="anonymous"></script>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<x-master-layout>
    <div class="viewport height-100">
        <div class="position-fixed w-100 h-100 justify-content-center align-items-center" id="loading" style="z-index: 100000;top:0;right0; background-color:#ffffff75;display:flex;">
            <img src="{{ asset('images/loading.gif') }}" class="h-25" alt="">
        </div>
    
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
        <div class="sidenav-overlay"></div>
        <div class="drag-target"></div>
        
        @include('layouts.footer')
    </div>
    @include('layouts.js')
    @yield('js')
    <script>
        function confirmDelete(msg){
            return confirm(msg ?? 'Are you Sure to Delete');
        }

        $('document').ready(function(){
            setTimeout(function(){
                $('#loading').fadeOut();
            },1000)
        })

        $('body').on('submit','form',function(){
            $('#loading').fadeIn();
            setTimeout(function(){
                $('#loading').fadeOut();
            },5000)
        })
    </script>

    {{-- Livewire Js Section start here --}}
    <livewire:scripts>

    @stack('js')
    @yield('lvjs')
    @stack('lvjs-stack')

</x-master-layout>
@yield('modal')
<!-- END: Body-->

</html>

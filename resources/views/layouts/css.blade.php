<title>Home Delivery Br</title>
<link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

@php
    $invoiceRoutes = ['admin.payment-invoices.index', 'admin.payment-invoices.invoice.index', 'admin.payment-invoices.invoice.edit', 'admin.payment-invoices.invoice.checkout.index'];
    $toasterRoutes = ['admin.orders.index', 'admin.orders.edit', 'admin.orders.show', 'admin.trash-orders.index'];
@endphp
<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}"> --}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/tether-theme-arrows.css') }}"> --}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/tether.min.css') }}"> --}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/shepherd-theme-default.css') }}"> --}}
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}"> -->

{{-- Toggleable Css --}}
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">

@if(Route::currentRouteName() === 'home')
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/card-analytics.css') }}">
@endif
<!-- END: Page CSS-->
{{-- <link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet"> --}}
@if(in_array(Route::currentRouteName(), $invoiceRoutes))
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/invoice.css') }}">
@endif

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@if(in_array(Route::currentRouteName(), $toasterRoutes))
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}">
@endif
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}"> --}}

<style>
    .border-0{
        border-width: 0px !important;
    }
    .border-1{
        border-width: 2px !important;
    }
    .border-2{
        border-width: 4px !important;
    }
    .border-3{
        border-width: 8px !important;
    }
    .border-4{
        border-width: 16px !important;
    }
    .border-5{
        border-width: 32px !important;
    }
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
    .modal-backdrop{
        zoom: 1.4;
    }
    .modal-backdrop {
        opacity : 0 !important;
    }
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
        *{
            -webkit-print-color-adjust: exact;
        },
        .print{
            display: block !important;
        }
    }

    ::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar
    {
        height: 5px;
        width: 6px;
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar-thumb
    {
        border-radius: 7px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
        background-color: #555;
    }
    .min-vh-100{
        min-height: 100vh !important
    }
    
    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
        #main-menu-navigation {
            overflow-y: scroll;
        }
    }

   /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {
        #main-menu-navigation {
            overflow-y: scroll;
        }
    }

    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {
        #main-menu-navigation {
            overflow-y: scroll;
        }
    }

    /* Extra large devices (large laptops and desktops, 1200px and up) */
    @media only screen and (min-width: 1200px) {
        #main-menu-navigation {
            overflow-y: scroll;
        }
    }

    @media only screen and (max-width: 768px) {
    .modal{
            padding-right: 153px;
        }
        .modal-content {
            width: min-content;
        }
    }    
    .dropdown-menu.overlap-menu {
        z-index: 10;
        right: 0px;
        left: unset !important;
    }

    .table-responsive-md{
        min-height: 265px;
    }
    .table-responsive.order-table {
        padding-bottom: 3rem;
    }

    .big-checkbox{
        height: 25px; 
        width: 25px;
    }
    .big-label {
        font-size: 15px;
    }

    .admin-api-settings {
        height: 20px;
        width: 20px;
        vertical-align: middle;
        margin-top: auto;
    }

    .modal-head {
        background-color: #fa857d !important;
        color: white;
    }

    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst {
        margin-right:3px;
    }
    .main-menu.menu-light .navigation#main-menu-navigation li a img{
        width: 16px;
    }
    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst:before{
        font-size:1.28rem;
    }
    .green{
        color:#3CB64B;
    }
    .red{
        color:#ff5a5a
    }
    .font-sans{
        font-family: sans-serif;
    }
    .search-header{
        display: table-header-group;
    }
</style>
@yield('custom-css')
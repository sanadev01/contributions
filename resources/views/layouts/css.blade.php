<title>Home Delivery Br</title>
<link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

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
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">

{{-- Toggleable Css --}}
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">


<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/card-analytics.css') }}">
<!-- END: Page CSS-->
<link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('app-assets/css/pages/invoice.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">

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
    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst {
        margin-right:4px;
    }

    .main-menu.menu-light .navigation#main-menu-navigation li a img{
        width: 16px;
    }
    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst {
        margin-right: 3px;
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
    .main-menu.menu-light .navigation > li ul .active {
        box-shadow: 0 0 0px 0px rgb(255 255 255 / 0%);
        border-radius: 4px !important;
        background: #1174b7 !important;
    }
    .main-menu.menu-light .navigation > li.active > a {
        background:  #1174b7;
        box-shadow: 0 0 0px 0px rgb(255 255 255 / 0%);
        color: #fff;
        font-weight: 400;
        border-radius: 4px;
    }
    .btn-primary {
        border-color: #1174b7 !important;
        background-color: #1174b7 !important;
        /* color: #fff; */
    }
    .btn-info {
        border-color: #1174b7 !important;
        background-color: #1174b7 !important;
        color: #fff !important;
    }
    /* th {
        background-color: blue;
        color: white;
    }  */
    .table thead th {
        background-color: #1174b7;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #dbe9f2 !important;
    }
    .bg-danger-custom {
        background-color: #fa9595
    }
    .table th, .table td {
        border-top: 0px solid #f8f8f8 !important;
    }
    .vs-radio-con .vs-radio .vs-radio--border {
        background: transparent;
        border: 2px solid #1174b7;
    }
    .font-large-1 {
        text-align: center;
        font-size: 1.5rem !important;
    }
    .vs-radio-con input:checked ~ .vs-radio .vs-radio--circle {
        background: #1174b7;
    }
    .table td, .table th {
        vertical-align: middle;
    }

    .app-content .wizard > .steps > ul > li.current .step {
        border-color: #1174b7 !important;
        background-color: #1174b7 !important;
    }

    .app-content .wizard > .steps > ul > li.current > a {
        color: #1174b7;
        cursor: default;
    }

    .app-content .wizard.wizard-circle > .steps > ul > li:before, .app-content .wizard.wizard-circle > .steps > ul > li:after {
        background-color: #1174b7 !important;
    }
    .app-content .wizard.wizard-circle > .steps > ul > li.current ~ li:before {
        background-color: transparent !important;
    }
    .app-content .wizard.wizard-circle > .steps > ul > li.current ~ li:after {
        background-color: transparent !important;
    }
    .app-content .wizard.wizard-circle > .steps > ul > li.current:after {
        background-color: transparent !important;
    }
    .app-content .wizard > .actions > ul > li > a {
        background: #1174b7 !important;
    }
    .hd-search:focus {
        box-shadow: 0 0 10px #1174b7;
        border-radius: 28px;
        border: 2px solid #1174b7;
        height: 40px;
    }
    .hd-search {
        border-radius: 28px;
        border: 1px solid #1174b7;
        height: 40px;
    }
    .hd-card {
        padding: 12px 16px !important;
        background: #dbe9f2;
        margin: -17px;
    }
    .hd-mt-22{
        margin-top:2.2rem!important
    }
    .mt-25 {
        margin-top:26px !important
    }

    .nav.nav-pills .nav-item .nav-link {
        border-radius: 10px 55px 10px 10px;
        border: solid 1px !important;
        border-color: #1174b7 !important;
        color: #1174b7;
    }

    .btn-primary:hover {
        border-color: #1174b7 !important;
        color: #fff !important;
        box-shadow: 0 8px 25px -8px #1174b7;
    }
    .hd-mt-20{
        margin-top:1.9rem!important
    }
</style>
@yield('custom-css')

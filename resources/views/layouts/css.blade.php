<title>Home Delivery Br</title>
<link rel="apple-touch-icon" href="{{ asset('images/hd-label-logo.png') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/hd-label-logo.png') }}">
{{-- <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet"> --}}

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
    .body {
        zoom: 0%;
        background-color: #f0f0f5 !important;
    }

    body.vertical-layout.vertical-menu-modern.menu-collapsed .sub-category span {
        display: none;
    }

    body.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .sub-category span {
        display: block !important;
    }

    .activityHeader {
        color: black !important;
        justify-content: center !important;
    }

    .loader-center {
        padding-left: 54%;
        padding-top: 23%;
    }

    .activityHead {
        justify-content: center !important;
    }

    .sub-category span {
        padding: 12px 30px 2px 20px;
        color: #d4d4d9 !important;
        font-family: "IBM Plex Sans", sans-serif !important;
        font-weight: 400 !important;
        font-size: 11px !important;
        letter-spacing: 0.5px;
        line-height: 12px;
    }

    .li .sub-category {
        margin-bottom: 0px;
    }

    #togglers {
        /* height: 50px; */
    }

    /* #datefilters{
        height: 60px;
    } */
    .icon-disc:before {
        color: white;
    }

    .icon-disc:after {
        color: white;
    }

    .header-navbar {
        font-family: 'IBM Plex Sans';
    }

    body.vertical-layout.vertical-menu-modern .toggle-icon {
        color: white !important;
    }

    .sub-category:not(:first-child) {
        margin-top: 0rem;
        font-size: 15px;
        padding-left: 7px;
    }

    .custom-border-25 {
        border-radius: 25px;
    }

    .navigation li a svg {
        width: 34px;
        text-align: center;
    }

    #main-menu-navigation {
        height: 100% !important;
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation>li>a>span {
        vertical-align: middle;
    }

    svg {
        vertical-align: middle;
    }

    .table-bordered thead td,
    .table-bordered thead th {
        border-bottom-width: 2px;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    .order-id {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .table-bordered tbody td {
        font-size: 15px !important;
        font-weight: 400 !important;
        line-height: 16px !important;
    }

    .corrioes-lable {
        border-radius: 10px;
    }

    #status-btn {
        width: 125px;
    }

    #printBtnDiv {
        padding-top: 0px;
        display: none;
    }

    .btnsDiv {
        padding-left: initial;
    }

    .dark-mode {
        background-color: #1a1a3c !important;
        color: white !important;
        background: none #1a1a3c !important;
    }

    #userNameCol {
        /* width: 300px; */
    }

    .fa-sort {
        padding-right: 0%;
    }

    .custom-sort-arrow {
        float: right;
        padding-top: 5px;
    }

    .vs-checkbox-con .vs-checkbox {
        border-width: 1px !important;
    }

    #imageDecrption {
        padding-left: 50px !important;
        padding-top: 7px !important;
        font-weight: 400 !important;
        margin-top: -2px;
        color: #74829c !important;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    #imageDecrptionParcel {
        /* padding-left: 40px !important; */
        vertical-align: center;
        padding-top: 7px !important;
        font-weight: 400 !important;
        margin-top: -2px;
        color: #74829c !important;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .searchDateBtn {
        margin-top: 24px !important;
    }

    .custom-margin-hr {
        margin-bottom: 7px;
        margin-top: 7px;
    }

    #hiddenSearch {
        display: none;
    }

    @media only screen and (min-width: 1200px) and (max-width: 1800px) {
        #myChart {
            height: 400px !important;
        }
    }

    @media only screen and (min-width: 1650px) {
        #myChart {
            height: 700px !important;
        }

        .crd-height-custom {
            height: 96.4% !important;
        }

        .custom-margin-hr {
            margin-bottom: 22px !important;
            margin-top: 22px !important;
        }
    }

    /* @media only screen and (min-width: 1200px) {
        .custom-margin-hr {
            margin-bottom: 0px;
            margin-top: 0px;
        }
    }

    @media only screen and (min-width: 1300px) {
        .custom-margin-hr {
            margin-bottom: 6px;
            margin-top: 6px;
        }
    }

    @media only screen and (min-width: 1400px) {
        .custom-margin-hr {
            margin-bottom: 8px;
            margin-top: 8px;
        }
    }

    @media only screen and (min-width: 1500px) {
        .custom-margin-hr {
            margin-bottom: 10.1px;
            margin-top: 10.1px;
        }
    }

    @media only screen and (min-width: 1600px) {
        .custom-margin-hr {
            margin-bottom: 12px;
            margin-top: 11.8px;
        }
    }

    @media only screen and (min-width: 1700px) {
        .custom-margin-hr {
            margin-bottom: 13.8px;
            margin-top: 13.8px;
        }
    }

    @media only screen and (min-width: 1800px) {
        .custom-margin-hr {
            margin-bottom: 15.9px;
            margin-top: 15.8px;
        }
    }

    @media only screen and (min-width: 1900px) {
        .custom-margin-hr {
            margin-bottom: 17.8px;
            margin-top: 17.7px;
        }
    } */

    /* .custom-margin-hr {
        margin-bottom: 0.5rem;
        margin-top: 0.54rem;
    } */
    /*
    @media (min-width:1500px) {
        .custom-margin-hr {
            margin-bottom: 0.8rem;
            margin-top: 0.8rem;
        }
    } */

    /* @media (min-width:1700px) {
        .custom-margin-hr {
            margin-bottom: 1rem;
            margin-top: 1rem;
        }
    } */

    .btn-width-sm {
        width: 73px;
    }

    .btn-width-md {
        width: 127px;
    }

    #imageDecrptionTop {
        padding-left: 50px !important;
        font-weight: 400 !important;
        margin-top: -38px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    #imageDecrptionTopParcel {
        vertical-align: center;
        /* padding-left: 40px !important; */
        font-weight: 400 !important;
        /* margin-top: -38px; */
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .card-dark {
        background-color: #2a2a4a !important;
        color: white !important;
        text: white !important;
    }

    .vs-checkbox-con input {
        width: 13px !important;
        margin: 0px;
    }

    .vs-checkbox-con {
        margin: 0px;
    }

    .vs-checkbox {
        margin: 0px;
        margin-right: 0px !important;
        margin-left: 0px !important;
    }

    .navbar-dark {
        background-color: #1a1a3c !important;
        color: white !important;
        background: none #1a1a3c !important;
    }

    .h3 {
        color: white !important;
    }
    .text-black {
        color: black !important;
    }

    .h6 {
        color: white !important;
    }

    .circleBase {
        border-radius: 32%;
        behavior: url(PIE.htc);
        /* remove if you don't care about IE8 */
    }

    .btn-primary {
        background-color: #5784BA !important;
    }

    .type2 {
        width: 40px;
        height: 40px;
        background: #ccc;
        top: 10%;
        right: 3%;
        bottom: 60px;
        z-index: 999;
        background-color: #5784BA;
        /* padding-left: 97.6%; */
        font-size: 1.7rem;
        vertical-align: center;
    }

    /* #colPhone, #colAddr, #colCnjp, #colActions{
        display: none;
    } */
    .th {
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
    }

    .table-dark-th {
        font-size: 14px;
        line-height: 21px;
        font-weight: 500;
    }

    #example {
        width: 100% !important;
    }

    .date-toggle-btn {
        padding-top: 7%;
        right: 4%;
        bottom: 60px;
        padding-left: 20%;
        font-size: 1.7rem;
        vertical-align: center;
    }

    .icon-plus-square {
        cursor: pointer;
    }

    #openEditModal {
        cursor: pointer;
    }

    #optionChkbx {
        width: 30px !important;
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation>li>a>i:before {
        height: 15px;
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub>a:not(.mm-next):after {
        padding-top: 6px;
        font-size: 0.7rem !important;
    }

    #pink {
        color: pink;
    }

    .main-menu {
        background-color: #5784BA !important;
        color: white !important;
    }

    .navigation {
        background-color: #5784BA !important;
        color: white !important;
    }

    .navigation li a {
        color: white !important;
        /* padding: 10px 0px 8px 15px !important; */
        font-weight: 400 !important;
        font-size: 15px;
        padding: 10px 24px;
        font-family: "IBM Plex Sans", sans-serif !important;
    }

    .navigation .main-menu {
        background-color: #5784BA !important;
        color: white !important;
    }

    .main-menu.menu-light .navigation li.has-sub ul.menu-content {
        background-color: #5784BA !important;
        color: white !important;

    }

    .main-menu.menu-light .navigation>li.open>a {
        background-color: #5784BA !important;
        color: white !important;
    }

    .filter-btn {
        display: flex;
        justify-content: flex-end;
        width: 101%;
    }

    .card {
        border-radius: 8px;
    }

    .activityCard {
        border-radius: 8px;
        height: 94%;
    }

    .notification-card-right {
        border-radius: 8px 8px 0px 0px;
    }

    .smallCharts {
        display: flex !important;
        justify-content: space-between !important;
    }

    #chart3 {
        width: 59px !important;
    }

    #cardChart,
    #chart2,
    #chart4 {
        width: 48px !important;
        height: 59px !important;
    }

    #firstCard,
    #secondCard,
    #thirdCard,
    #fourthCard {
        padding-left: 16px;
    }

    .figures {
        font-weight: 600 !important;
        font-size: 1.75rem;
        font-size: 28px;
    }

    .notification-card-right {
        background-color: #5784BA;
        width: 100%;
        color: white !important;
        display: flex;
        justify-content: center;
    }

    .chartsRow {
        padding-top: 16px;
    }

    #dateSearch {
        display: none;
    }

    #searchBlock {
        display: none;
    }

    #downloadsDiv {
        display: none
    }

    #singleSearch {
        display: none;
        padding-top: 8px !important;

    }

    .hide {
        display: none !important;
    }

    #userSearch {
        display: none;
    }

    .paddinglr {
        padding-left: 120px;
        padding-right: 120px;
    }

    .singleSearchStyle {
        padding-right: 0px !important;
    }

    .ps__rail-x {
        background-color: transparent !important;
    }

    .media-left {
        padding-right: 5px;
    }

    .media-body {
        padding-top: 2px;
    }

    .media-meta {
        padding-top: 1px;
        padding-right: 16px;
    }

    .border-0 {
        border-width: 0px !important;
    }

    .border-1 {
        border-width: 2px !important;
    }

    .border-2 {
        border-width: 4px !important;
    }

    .border-3 {
        border-width: 8px !important;
    }

    .border-4 {
        border-width: 16px !important;
    }

    .border-5 {
        border-width: 32px !important;
    }

    .modal {
        background-color: #2e2e2e9e;
    }

    .picker__holder {
        bottom: 100% !important;
    }

    .alert-warning {
        background: rgba(231, 0, 0, 0.44) !important;
        color: #000000 !important;
    }

    .modal-backdrop {
        zoom: 1.4;
    }

    .modal-backdrop {
        opacity: 0 !important;
    }

    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }

        * {
            -webkit-print-color-adjust: exact;
        }

        ,
        .print {
            display: block !important;
        }
    }

    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar {
        height: 5px;
        width: 6px;
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar-thumb {
        border-radius: 7px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
        background-color: #555;
    }

    .min-vh-100 {
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
            scrollbar-color: transparent !important;
        }
    }

    @media only screen and (max-width: 768px) {
        .modal {
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

    .dropdown-menu.overlap-menu-order {
        transform: translate3d(57px, 25px, 0px) !important;
        right: none !important;
    }

    .table-responsive-md {
        min-height: 265px;
    }

    .table-responsive.order-table {
        padding-bottom: 3rem;
    }

    .big-checkbox {
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

    .btn[class*="btn-outline-"] {
    padding-top: 6px !important;
    padding-bottom: 5px !important;
}

    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst {
        margin-right: 4px;
    }

    .main-menu.menu-light .navigation#main-menu-navigation li a img {
        width: 16px;
    }

    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst {
        margin-right: 3px;
    }

    .main-menu.menu-light .navigation#main-menu-navigation li a .icon_adjst:before {
        font-size: 1.28rem;
    }

    .green {
        color: #3CB64B;
    }

    .red {
        color: #ff5a5a
    }

    .main-menu.menu-light .navigation>li ul .active {
        box-shadow: 0 0 0px 0px rgb(255 255 255 / 0%);
        border-radius: 4px !important;
        padding-top: 8px !important;
        background: #1174b7 !important;
        height: 46px;
        /* background: transparent; */
    }

    .main-menu.menu-light .navigation li a {
        padding: 2px 30px 2px 20px !important;
    }

    .main-menu.menu-light .navigation>li.active>a {
        background: #1174b7;
        /* background: transparent; */
        padding-top: 8px !important;
        box-shadow: 0 0 0px 0px rgb(255 255 255 / 0%);
        color: #fff;
        font-weight: 400;
        border-radius: 4px;
        /* color: var(--primary-bg-color) !important; */
    }

    .btn-info {
        border-color: #5784BA !important;
        background-color: #5784BA !important;
        color: #fff !important;
    }

    /* th {
        background-color: blue;
        color: white;
    }  */
    .table thead th {
        /* border-bottom: 1px #dee2e6 !important; */
        padding: 0.75rem !important;
    }

    /* tr:nth-child(even) {
        background-color: #dbe9f2 !important;
    } */
    .bg-danger-custom {
        background-color: #f5d9d9
    }

    .table th,
    .table td {
        /* border-top: 0px solid #f8f8f8 !important; */
    }

    .vs-radio-con .vs-radio .vs-radio--border {
        background: transparent;
        border: 2px solid #1174b7;
    }

    .font-large-1 {
        text-align: center;
        font-size: 1.5rem !important;
    }

    .vs-radio-con input:checked~.vs-radio .vs-radio--circle {
        background: #1174b7;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .app-content .wizard>.steps>ul>li.current .step {
        border-color: #1174b7 !important;
        background-color: #1174b7 !important;
    }

    .app-content .wizard>.steps>ul>li.current>a {
        color: #1174b7;
        cursor: default;
    }

    .app-content .wizard.wizard-circle>.steps>ul>li:before,
    .app-content .wizard.wizard-circle>.steps>ul>li:after {
        background-color: #1174b7 !important;
    }

    .app-content .wizard.wizard-circle>.steps>ul>li.current~li:before {
        background-color: transparent !important;
    }

    .app-content .wizard.wizard-circle>.steps>ul>li.current~li:after {
        background-color: transparent !important;
    }

    .app-content .wizard.wizard-circle>.steps>ul>li.current:after {
        background-color: transparent !important;
    }

    .app-content .wizard>.actions>ul>li>a {
        background: #1174b7 !important;
    }

    /* .hd-search:focus {
        box-shadow: 0 0 10px #1174b7;
        border-radius: 28px;
        border: 2px solid #1174b7;
        height: 40px;
    } */
    /* .hd-search {
        border-radius: 28px;
        border: 1px solid #1174b7;
        height: 40px;
    } */
    .hd-card {
        /* padding: 12px 16px !important; */
        /* background: #dbe9f2; */
        margin: -17px;
    }

    .hd-mt-22 {
        margin-top: 2.2rem !important
    }

    .mt-25 {
        margin-top: 26px !important
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

    .hd-mt-20 {
        margin-top: 1.9rem !important
    }

    .loader1,
    .loader2,
    .loader3,
    .loader4,
    .loader5,
    .loader6,
    .loader7 {
        display: none;
        height: 30%;
    }

    .parcels .loader1,
    .consolidation .loader1,
    .orders .loader1,
    .trash-orders .loader7,
    .activity .loader7,
    .profile .loader7,
    .roles .loader7,
    .users .loader7,
    .shcode .loader7,
    .addresses .loader7,
    .connect .loader7,
    .import .loader7,
    .reports .loader7,
    .label .loader7,
    .scan-label .loader7 {
        display: block;
    }

    .width-100 {
        width: 100px;
    }

    .padding-left {
        padding-left: 8%;
    }

    .delivery_bill .loader2 {
        display: block;
    }

    .containers .loader3,
    .chile_containers .loader3,
    .sinerlog_containers .loader3,
    .usps_containers .loader3 {
        display: block;
    }

    .search_package .loader4,
    .scan .loader4 {
        display: block;
    }

    .rates .loader5,
    .handling-services .loader5,
    .shipping-services .loader5,
    .payment-invoices .loader5,
    .deposit .loader5,
    .billing-information .loader5 {
        display: block;
        height: 20%;
    }

    .tracking .loader5 {
        display: block;
    }

    .affiliate .loader5 {
        display: block
    }
    .no-resize {
        resize: none;
    }
    .modal-head {
        background-color: #fa857d !important;
        color: white;
    }
    .bg-white {
        background: white !important;
    }
    .dropdown .dropdown-menu::before {
        left: 8.5rem;
    }
    .dropdown-menu.overlap-menu {
        right: 37px;
    }
    .font-sans{
        font-family: sans-serif;
    }
    .search-header{
        display: table-header-group;
    }
</style>
@yield('custom-css')
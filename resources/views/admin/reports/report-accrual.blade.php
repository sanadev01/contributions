@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">

@endsection
@section('page')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section id="dashboard-analytics">
    <div class='row no-gutters d-flex align-items-center light-green-color'>
        <div class="col-xl-4 col-lg-12 light-green-color">
            <div class="light-green-color welcome-admin height-100">
                <div class="ml-3">
                    <dl>
                        <div class="font-weight-bold large-heading-text pt-3 "> Generator Report
                        </div>
                        <dd class="font-weight-light pb-2 mb-4">Your tax & Duty report is here.</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-12 light-green-color  border-radius-15 p-2">
            <div class="row mt-0 ">
                <div class="col-12 pb-xl-2 pb-1 h-25">
                    <div class="filter-card " id="filter-card">
                        <form action="{{ route('admin.reports.kpi-report.store') }}" method="POST">
                            <div class="row">
                                @csrf

                                <div class="col-5">
                                    <label for="end-date" class="mt-4 mb-2 font-black"><strong>Start Date</strong></label><br>
                                    <div class="input-group">
                                        <input name="start_date" id="startDate" class="form-control py-2 mr-1 p-3" type="date">
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label for="end-date" class="mt-4 mb-2 font-black"><strong>End Date</strong></label><br>
                                    <div class="input-group">
                                        <input name="end_date" id="endDate" class="form-control py-2 mr-1 p-3" type="date">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mt-4 mb-2 font-black align-center float-center">
                                        <input type="hidden" name="type" value="accrual">
                                        <button type="submit" class="btn btn-success waves-effect waves-light p-3 mt-4" {{ true ? '' : 'disabled' }}> <i class="fa fa-download"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:order.accrual-table />
    @include('layouts.livewire.loading')
</section>
@endsection
@section('modal')
<x-modal />
@endsection
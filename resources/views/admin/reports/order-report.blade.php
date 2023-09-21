@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-0">
                    @section('title', __('orders.orders'))
                    <div class="row filter col-12 d-flex justify-content-end pr-2">
                        <a href="{{ route('admin.reports.order.create') }}" class="btn btn-success mr-1 mb-1"
                            title="@lang('orders.import-excel.Download')">
                            <i class="fa fa-arrow-down"></i>
                        </a>
                        <button type="btn" onclick="toggleHiddenSearch()" id="orderSearch"
                            class="btn btn-primary mb-1 waves-effect waves-light mr-1"><i
                                class="feather icon-search"></i></button>
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                            class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                                class="feather icon-filter"></i></button>
                    </div>

                    <div class="col-12 pr-0 m-0 pl-0" id="datefilters">
                        <div class=" col-12 text-left pl-0">
                            <div class="col-12 pl-0" id="dateSearch">
                                <form class="col-12 pl-0" action="{{ route('admin.order.exports') }}" method="GET"
                                    target="_blank">
                                    @csrf
                                    <div class="form-group mb-2 col-3 pl-1" style="float:left;margin-right:20px;">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control">
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2 col-3" style="float:left;margin-right:20px;">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control">
                                    </div>
                                    <button class="btn btn-success searchDateBtn">
                                        <i class="fa fa-arrow-down"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <livewire:reports.order-report-table />
                    </div>
                    <livewire:order.bulk-edit.modal />
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modal')
    <x-modal />
@endsection

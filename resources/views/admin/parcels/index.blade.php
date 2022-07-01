@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                    @section('title', __('parcel.My Parcels'))
                    <div class="col-6 pl-0">
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.consolidation.parcels.index') }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="btn btn-lg btn-info"> @lang('consolidation.Create Consolidation') </a>
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.parcels.create') }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="btn btn-lg btn-primary"> @lang('parcel.Create Parcel') </a>
                    </div>
                    <div class="row filter col-6 d-flex justify-content-end pr-4">
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                            class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                                class="feather icon-filter"></i></button>
                        <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch"
                            class="btn btn-primary mb-1 waves-effect waves-light"><i
                                class="feather icon-search"></i></button>
                    </div>
                </div>
                <div class="card-content card-body">
                    <div class="row col-12 pr-0 m-0 pl-1" id="datefilters">
                        <div class=" col-6 text-left mb-2 pl-0">
                            <div class="row col-12 my-3 pl-0" id="dateSearch">
                                <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                                    @csrf
                                    <div class="form-group mb-2 col-4" style="float:left;margin-right:20px;">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control">
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2 col-4" style="float:left;margin-right:20px;">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control">
                                    </div>
                                    <button class="btn btn-success searchDateBtn" title="@lang('orders.import-excel.Download')">
                                        <i class="fa fa-arrow-down"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="row col-8 pr-0 pl-1 " id="singleSearch">
                        <div class="form-group singleSearchStyle col-12">
                            <form wire:submit.prevent="submit">
                                <div class="form-group mb-2 col-12 row">
                                    {{-- <label class="col-12 text-left"> Search</label> --}}
                                    <input type="text" class="form-control col-8 hd-search">
                                    <button type="submit" class="btn btn-primary ml-2">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive-md mt-1">
                        <livewire:pre-alert.table />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('modal')
<x-modal />
@endsection

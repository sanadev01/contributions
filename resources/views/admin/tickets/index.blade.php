@extends('layouts.master')

@section('page')
    @can('reply',App\Models\Ticket::class)
        @section('title', __('tickets.Support Tickets'))
    @endcan
    @cannot('reply',App\Models\Ticket::class)
        @section('title', __('tickets.My Tickets'))
    @endcan
@endif

<div class="card min-vh-100">
    <div class="card-header">
        <div class="col-8 btnsDiv" style="display: flex;">
            <div id="printBtnDiv">
                <button title="Print Labels" id="print" type="btn" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                    <i class="feather icon-printer"></i>
                </button>
                <button title="Print Domestic Labels" id="domesticPrint" type="btn" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                    <i class="feather icon-tag"></i>
                </button>
                <button title="Delete" id="trash" type="btn"
                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                    <i class="feather icon-trash"></i>
                </button>
            </div>
        </div>

        <div class="row filter" style="padding-right:0.5%;">
            <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch"
                class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-search"></i></button>

        </div>
    </div>

    <div class="card-content">
        @user
            <a href="{{ route('admin.tickets.create') }}" class="pull-right btn btn-primary"> @lang('tickets.Create New Ticket') </a>
        @enduser
        <div class="card-body no-print pt-0" style="overflow-y: visible">
            <livewire:tickets />
        </div>
    </div>
</div>

@endsection

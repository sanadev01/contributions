@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header d-flex justify-content-end pr-3">
                    @section('title', __('Balance Transactions'))
                    <button onclick="toggleLogsSearch()" class="btn btn-primary mr-1 waves-effect waves-light">
                        <i class="feather icon-search"></i>
                    </button>
                    <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                        class="btn btn-primary mr-1 waves-effect waves-light"><i
                            class="feather icon-filter"></i></button>
                    <a href="{{ route('admin.deposit.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add Balance
                    </a>
                </div>
                <div class="card-content">
                    <div class="table-responsive-md mt-1">
                        <livewire:deposit.table-component />
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

@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header pr-1">
                    @section('title', __('address.My Addresses'))
                    @can('create', App\Models\Address::class)
                        <div class="col-12 d-flex justify-content-end">
                            <button onclick="toggleLogsSearch()" class="mr-1 btn btn-primary waves-effect waves-light">
                                <i class="feather icon-search"></i>
                            </button>
                            <a href="{{ route('admin.addresses.create') }}" class="pull-right btn btn-primary">
                                @lang('address.Add Address') </a>
                        </div>
                    @endcan
                </div>
                <div class="card-content card-body">
                    <div class="table-responsive-md mt-1">
                        <livewire:address.table />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

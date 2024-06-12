@extends('layouts.master')

@section('page')
    <section> 
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('address.My Addresses') </h4>
                        @can('create', App\Models\Address::class)
                            <a href="{{ route('admin.addresses.create') }}" class="pull-right btn btn-primary"> @lang('address.Add Address') </a>
                        @endcan
                    </div>
                    <div class="card-content card-body">
                        <div class="table-responsive-md mt-1">
                            <livewire:address.table/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

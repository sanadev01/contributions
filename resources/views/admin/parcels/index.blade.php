@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                        <h1 class="mb-0">
                            @lang('parcel.My Parcels')
                        </h1>
                        <div>
                            @if(Auth::user()->status == "active")
                            <a href="{{ route('admin.consolidation.parcels.index') }}" class="btn btn-lg btn-info"> @lang('consolidation.Create Consolidation') </a>
                            <a href="{{ route('admin.parcels.create') }}" class="btn btn-lg btn-primary"> @lang('parcel.Create Parcel') </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="table-responsive-md mt-1"> 
                            <livewire:pre-alert.table/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <x-modal/>
@endsection
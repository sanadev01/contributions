@extends('layouts.master')
@section('page')
<div class="prealert" id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0">Show details</h1>
                    @admin
                    <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> Back to List </a>
                    @endadmin

                    @user
                    <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> Back to List </a>
                    @enduser
                </div>
                <hr>
                <div class="card-content">
                    <x-modals.pre-alert-detail :id="$prealert->id"></x-modals.pre-alert-detail>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

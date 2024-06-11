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
                            @can('consolidateParcel',App\Models\Order::class)
                            <a @if(Auth::user()->isActive()) href="{{ route('admin.consolidation.parcels.index') }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}"  @endif class="btn btn-lg btn-info"> @lang('consolidation.Create Consolidation') </a>
                            @endcan
                            <a @if(Auth::user()->isActive()) href="{{ route('admin.parcels.create') }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}"  @endif class="btn btn-lg btn-primary"> @lang('parcel.Create Parcel') </a>
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
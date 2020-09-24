@extends('layouts.master')

@section('page')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">@lang('orders.orders')</h4>
        {{-- <a href="{{ route('admin.roles.create') }}" class="pull-right btn btn-primary"> Create Role </a> --}}
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <livewire:order.table/>
        </div>
    </div>
</div>
@endsection


@section('modal')
    <x-modal/>
@endsection
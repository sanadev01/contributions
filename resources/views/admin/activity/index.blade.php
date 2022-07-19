@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="col-12 pt-0 d-flex justify-content-end "> --}}
                    {{-- wire:click.prevent="getActivities" --}}
                    {{-- <button class="mt-2 btn btn-primary" title="search" onclick="toggleLogsSearch()">
                        <i class="fa fa-search"></i>
                    </button>
                </div> --}}
                    <div class="card-header">
                    @section('title', __(' Activity Logs'))
                </div>
                <div class="card-content">
                    <div class="table-responsive-md mt-1">
                        <livewire:activity.table />
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

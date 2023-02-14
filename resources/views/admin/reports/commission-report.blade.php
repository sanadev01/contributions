@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header"> --}}
                @section('title', __('Commission Reports'))
                {{-- </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <livewire:reports.commission-report-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

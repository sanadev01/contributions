@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @section('title', __('Commission Reports'))
                    <div class="card-content">
                        <div class="card-body">
                            <livewire:reports.commission-report-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

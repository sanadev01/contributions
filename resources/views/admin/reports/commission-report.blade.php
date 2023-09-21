@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Commission Reports</h4>
                    </div>
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

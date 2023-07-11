@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <h4 class="mb-0">Export Anjun Report</h4>
                        </div>
                        <div class="card-body">
                            <livewire:order.export-order /> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

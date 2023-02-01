@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <livewire:order.export-order /> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

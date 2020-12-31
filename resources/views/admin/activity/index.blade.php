@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Activity Logs
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <livewire:activity.table/>
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
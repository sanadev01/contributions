@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('handlingservice.Manage Services')
                        </h4>
                        @can('create', App\Models\HandlingService::class)
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                            @lang('Create')
                        </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <livewire:tax.tax/>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

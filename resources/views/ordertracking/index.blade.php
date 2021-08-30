@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Track Your Order
                        </h4>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <livewire:order-tracking.trackings>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

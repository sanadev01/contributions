@extends('layouts.master')

@section('page')
<section id="prealerts">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-3">Amazon Selling Partner Orders</h4>
                </div>

                <div class="card-content">
                    <livewire:amazon-orders.table />
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
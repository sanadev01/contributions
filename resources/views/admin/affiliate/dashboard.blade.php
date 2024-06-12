@extends('layouts.master')

@section('css')
<script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.min.js') }}"></script>
@endsection

@section('page')
    <!-- Dashboard Analytics Start -->
    
    <section id="dashboard-analytics">
        <div class="row justify-content-center">
            <livewire:affiliate.stats.sale/>
            <livewire:affiliate.stats.commission/>
        </div>
        @admin
        <div class="row justify-content-center">
            <livewire:affiliate.stats.admin-sale/>
        </div>
        @endadmin

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <livewire:affiliate.stats.copy-to-clipboard/>
                    <div class="my-5"></div>
                    <livewire:affiliate.stats.barcode />
                </div>
            </div>
        </div>   
    </section>
    <!-- Dashboard Analytics end -->
@endsection

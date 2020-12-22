@extends('layouts.master')

@section('css')
<script src="{{ asset('app-assets/vendors/js/charts/echarts/echarts.min.js') }}"></script>
@endsection

@section('page')
    <!-- Dashboard Analytics Start -->
    
    <section id="dashboard-analytics">
        <div class="col-md-12 row">
            <livewire:affiliate.stats.sale/>
            <livewire:affiliate.stats.commission/>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <livewire:affiliate.stats.copy-to-clipboard/>
                </div>    
            </div>    
        </div>    
    </section>
    <!-- Dashboard Analytics end -->
@endsection

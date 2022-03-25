@extends('layouts.master')

@section('page') 
    <section id="app">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Scan Products</h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <scan-product :orders_prop="{{ json_encode($orders)}}" :base_url="{{ json_encode($baseUrl) }}"></scan-product>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        var window_hegiht = screen.height;
        var header_height = $('.header-navbar').css('height');
        var card_header_height = $('#app .card-header').css('height');
        var card_form_height = $('#card-form').css('height');
        var card_thead_height = $('#app .card thead').css('height');
        var total = parseFloat(header_height) + parseFloat(card_header_height) + parseFloat(card_form_height) + parseFloat(card_thead_height);
        var remaining_hegiht = window_hegiht - Math.round(total* 100) / 100;
        $('#app .card').css('height', remaining_hegiht);
    </script>
@endpush
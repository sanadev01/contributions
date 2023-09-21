@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        <button type="btn" onclick="toggleBlockSearch()" id="customSwitch8"
                            class="btn btn-primary  waves-effect waves-light"><i class="feather icon-filter"></i></button>
                    @section('title', __('menu.Driver Report'))
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <livewire:label.driver-report>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @lang('bpsleve.LEVE & BPS Rates')
                            </h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>@lang('bpsleve.BPS')</h3> 
                                    {{-- <livewire:components.shipping-service-toggle :service-id="getShippingServiceID('bps')"/> --}}
                                </div>
                                <div class="col-md-6">
                                    <h3>@lang('bpsleve.LEVE')</h3>
                                    {{-- <livewire:components.shipping-service-toggle :service-id="getShippingServiceID('leve')"/> --}}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.rates.bps-leve.create') }}" class="pull-right btn btn-primary">
                            @lang('bpsleve.Upload Rates')
                        </a>
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('bpsleve.Weight')
                            </div>
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('bpsleve.BPS') ($)
                            </div>
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('bpsleve.LEVE') ($)
                            </div>
                        </div>
                        @foreach($rates->data??[] as $rate)
                            <div class="row justify-content-center">
                                <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 text-center">
                                    {{ isset($rate['weight'])?$rate['weight']:0 }} g
                                </div>
                                <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 text-center">
                                    {{ isset($rate['bps'])?$rate['bps']:0 }}
                                </div>
                                <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 text-center">
                                    {{ isset($rate['leve'])?$rate['leve']:0 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

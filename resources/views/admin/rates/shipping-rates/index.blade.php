@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                @lang('shipping-rates.shipping-rates')
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.shipping-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('shipping-rates.Weight')
                            </div>
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('shipping-rates.BPS') ($)
                            </div>
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2 border col-xl-2 h4 text-center bg-primary text-white">
                                @lang('shipping-rates.LEVE') ($)
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

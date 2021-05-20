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
                            <a href="{{ route('admin.rates.accrual-rates.create') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Upload Rates')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        Service
                                    </th>

                                    <th>
                                        Weight (Grams)
                                    </th>

                                    <th>
                                        CWB
                                    </th>

                                    <th>
                                        GRU
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shippingRates as $rate)
                                    <tr>
                                        <th>
                                            {{ $rate->getServiceName() }}
                                        </th>

                                        <th>
                                            {{ $rate->weight }}
                                        </th>

                                        <th>
                                            ${{ $rate->cwb }}
                                        </th>

                                        <th>
                                            ${{ $rate->gru }}
                                        </th>
                                    </tr>
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

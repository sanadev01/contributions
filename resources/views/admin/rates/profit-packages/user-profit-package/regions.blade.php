@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('menu.Rates')</h4>
                    </div>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        Country
                                    </th>
                                    <th>
                                        Region
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shippingRegionRates as $rate)
                                    <tr>
                                        <th>
                                            {{ optional($rate->country)->name }}
                                        </th>
                                        <th>
                                            {{ optional($rate->region)->name }}
                                        </th>
                                        <th>
                                            <form action="{{ route('admin.rates.show-profit-region-rates') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="serviceId" value="{{ $rate->shipping_service_id }}">
                                                <input type="hidden" name="id" value="{{ $rate->id }}">
                                                <button type="submit" class="btn btn-success btn-sm"> View </button>
                                            </form>
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

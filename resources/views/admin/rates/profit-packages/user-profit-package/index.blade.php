@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('menu.Rates')</h4>
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th> Service </th>
                                    <th>
                                        Rates
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $rateSetting)
                                        <tr>
                                            <td>
                                                {{ $rateSetting->shippingService->name }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.rates.show-profit-rates') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="serviceId" value="{{ $rateSetting->service_id }}">
                                                    <input type="hidden" name="serviceName" value="{{ $rateSetting->shippingService->name }}">
                                                    <input type="hidden" name="packageId" value="{{ $rateSetting->package_id }}">
                                                    <button type="submit" class="btn btn-success btn-sm">View Rates</button>
                                                </form>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                    @foreach($rates as $rate)
                                        <tr>
                                            <td>
                                                {{ $rate['service'] }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.rates.show-service-rates') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="rates" value="{{ $rate['rates'] }}">
                                                    <input type="hidden" name="serviceId" value="{{ $rate['service'] }}">
                                                    <input type="hidden" name="packageId" value="{{ $rate['packageId'] }}">
                                                    <button type="submit" class="btn btn-success btn-sm">View Rates</button>
                                                </form>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

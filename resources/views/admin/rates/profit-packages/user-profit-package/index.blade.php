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
                                    @foreach($settings as $setting)
                                        <tr>
                                            <td>
                                                {{ $setting->shippingService->name }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.rates.show-profit-rates', ['id'=>$setting->service_id,'packageId'=>$setting->package_id]) }}" class="btn btn-success btn-sm">View Rates</a>
                                            </td>
                                            
                                        </tr>
                                        @endforeach
                                        @if($services)
                                            @foreach($services as $service)
                                                <tr>
                                                    <td>
                                                        {{ $service->name }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.rates.show-profit-rates', ['id'=>$service->id,'packageId'=>$service->id] ) }}" class="btn btn-success btn-sm">View Rates</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

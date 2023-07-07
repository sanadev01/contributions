@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row pt-3 pl-2">
                        <div class="col-6">
                            <h4 class="mb-0">{{ $serviceRates->shippingService->name }}  @lang('menu.Rates')</h4>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <a href="" class="btn btn-success"> @lang('profitpackage.download-profit-package') <i class="feather icon-download"> </i></a>
                            <a href="{{ route('admin.rates.user-rates.index') }}" class="btn btn-primary mx-3">@lang('profitpackage.back to list')</a>  
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th> Weight </th>
                                    <th>
                                        Cost
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceRates->data as $rate)
                                        <tr>
                                            <td>
                                                {{ optional($rate)['weight'] . ' g' }}
                                            </td>
                                            <td>
                                                {{ $rate['leve'] }}
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

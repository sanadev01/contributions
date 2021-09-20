@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row pt-3 pl-2">
                        <div class="col-6">
                            <h4 class="mb-0">{{ $service }}  @lang('menu.Rates')</h4>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <a href="{{ route('admin.rates.rates.exports',$packageId) }}" class="btn btn-success"> @lang('profitpackage.download-profit-package') <i class="feather icon-download"> </i></a>
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
                                    @foreach($rates as $rate)
                                        <tr>
                                            <td>
                                                {{ $rate['weight'] . ' g' }}
                                            </td>
                                            <td>
                                                {{ number_format($rate['shipping'][0]*($rate['profit']/100)+$rate['shipping'][0],2) }}
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

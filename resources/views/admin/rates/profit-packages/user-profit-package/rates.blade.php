@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row pt-3 pl-2">
                        <div class="col-6">
                            <h4 class="mb-0">{{ $service->name }}  @lang('menu.Rates')</h4>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            @if($isGDE)
                                <a href="{{ route('admin.rates.rates.exports', $service) }}" class="btn btn-success"> @lang('profitpackage.download-profit-package') <i class="feather icon-download"> </i></a>
                            @else
                                <a href="{{ route('admin.rates.rates.exports',$packageId) }}" class="btn btn-success"> @lang('profitpackage.download-profit-package') <i class="feather icon-download"> </i></a>
                            @endif
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
                                                {{ optional($rate)['weight'] . ' g' }}
                                            </td>
                                            <td>
                                                @if ($service->name == 'Brazil Redispatch')
                                                    ${{ optional($rate)['leve'] }}
                                                @elseif($isGDE)
                                                    {{ number_format(($profit / 100) * $rate['leve'] + $rate['leve'], 2) }}
                                                @else    
                                                    {{ number_format(optional(optional($rate)['shipping'])[0]*(optional($rate)['profit']/100)+optional(optional($rate)['shipping'])[0],2) }}
                                                @endif
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

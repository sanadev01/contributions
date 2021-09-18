@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('menu.Rates')</h4>
                        {{-- <a href="{{ route('admin.rates.rates.exports',$packageId) }}" class="pull-right btn btn-success"> @lang('profitpackage.download-profit-package') <i class="feather icon-download"> </i></a> --}}
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
                                    @foreach($rates as $rate)
                                        <tr>
                                            <td>
                                                {{ $rate['service'] }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.rates.show-profit-rates') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="rates" value="{{ $rate['rates'] }}">
                                                    <button type="submit" class="btn btn-success btn-sm">View Rates</button>
                                                </form>
                                                {{-- <a href="{{ route('admin.rates.show-profit-rates', 123) }}" type="button" class="btn btn-success btn-sm">View Rates</a> --}}
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

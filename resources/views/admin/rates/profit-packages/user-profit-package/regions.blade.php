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
                                @foreach ($shippingRegions as $region)
                                    <tr>
                                        <td>
                                            {{ optional($region->country)->name }}
                                        </td>
                                        <td>
                                            {{ optional($region->region)->name }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.rates.show-profit-rates', ['id'=>$region->id,'packageId'=>'region'] ) }}" class="btn btn-success btn-sm">View Rates</a>
                                        </td>
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

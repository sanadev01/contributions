@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                            @section('title', __('shipping-rates.shipping-rates') . 'For' .
                                optional($shipping_rate->shippingService)->name)
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                            <a href="{{ route('admin.rates.shipping-rates.index') }}" class="pull-right btn btn-primary">
                                @lang('shipping-rates.Return to List')
                            </a>
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>
                                        Weight
                                    </th>
                                    <th>
                                        Rates ($)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shipping_rate->data??[] as $rate)
                                    <tr>
                                        <th>
                                            {{ isset($rate['weight']) ? $rate['weight'] : 0 }} g
                                        </th>
                                        <th>
                                            {{ isset($rate['leve']) ? $rate['leve'] : 0 }}
                                        </th>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            No data found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </section>
@endsection

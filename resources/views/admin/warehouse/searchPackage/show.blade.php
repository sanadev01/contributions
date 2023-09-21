@extends('layouts.master')

@section('page')
    <section id="vue-scanner">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Search @lang('warehouse.containers.Packages Inside Container')
                        </h4>
                        <div>
                            <a href="{{ route('warehouse.search_package.index') }}" class="btn btn-primary"> <i
                                    class="fa fa-search"></i> @lang('Search Packages') </a>
                        </div>
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <table class="table table-responsive-md mb-0">
                                <tbody>
                                    <tr>
                                        <th> Barcode </th>
                                        <td> {{ $order->corrios_tracking_code }} </td>
                                    </tr>
                                    <tr>
                                        <th> Client </th>
                                        <td> {{ $order->merchant }} </td>
                                    </tr>
                                    <tr>
                                        <th> Dimensions </th>
                                        <td> {{ $order->length . ' x ' . $order->length . ' x ' . $order->height }} </td>
                                    </tr>
                                    <tr>
                                        <th> Weight </th>
                                        <td> {{ $order->getWeight('kg') . ' kg (' . $order->getWeight('lbs') }} lbs) </td>
                                    </tr>
                                    <tr>
                                        <th> Reference# </th>
                                        <td> {{ $order->warehouse_number }} </td>
                                    </tr>
                                    <tr>
                                        <th> Recpient </th>
                                        <td> {{ $order->recipient->first_name . ' ' . $order->recipient->last_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <h4>Bag Information</h4>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Dispatch Number</th>
                                        <td>{{ optional(optional($order->containers)[0])->dispatch_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Seal</th>
                                        <td>{{ optional(optional($order->containers)[0])->seal_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Container Type</th>
                                        <td>{{ optional(optional($order->containers)[0])->getContainerType() }}</td>
                                    </tr>
                                    <tr>
                                        <th>Destination Airport</th>
                                        <td>{{ optional(optional($order->containers)[0])->destination_operator_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>AWB#</th>
                                        <td>{{ optional(optional($order->containers)[0])->awb }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<section class="card invoice-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-wrapper position-relative">
                    <table class="table mb-0 table-responsive-md table-striped">
                        <thead>
                            <tr class="text-danger">
                                <th>{{ $order->corrios_tracking_code }}</th>
                                <th>{{ $order->warehouse_number }}</th>
                                <th>{{ optional($order)->weight }} {{ optional($order)->measurement_unit }}</th>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>@if($order->recipient->country_id == \App\Models\Order::US)City @else Country @endif</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->trackings as $track)
                            <tr>
                                <td>
                                    {{ $track->created_at }}
                                </td>
                                <td>
                                    @if($order->recipient->country_id == \App\Models\Order::US) {{ $track->city }} @else {{ $track->country }} @endif
                                </td>
                                <td>
                                    {{ $track->description }}
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <hr>
                    <table class="table mb-0 table-responsive-md table-striped">
                        <tr>
                            <h3 class="text-center">Bag Information</h3>
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
                            <td>{{ optional(optional($order->containers)[0])->container_type }}</td>
                        </tr>
                        <tr>
                            <th>Destination Airport</th>
                            <td>{{ optional(optional($order->containers)[0])->destination_operator_name }}</td>
                        </tr>
                        <tr>
                            <th>AWB#</th>
                            <td>{{ optional(optional($order->containers)[0])->awb }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>
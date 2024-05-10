<div class="row">
    <div class="table-responsive-md col-12">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>@lang('orders.invoice.merchant')</th>
                    <th>@lang('orders.invoice.carrier')</th>
                    <th>@lang('orders.invoice.carrier_tracking')</th>
                    <th>@lang('orders.invoice.whr_number')</th>
                    <th>@lang('orders.invoice.customer_reference')</th>
                    <th>@lang('orders.invoice.tracking_code')</th>
                </tr>
                <tr>
                    <td>{{ $parcel->merchant }}</td>
                    <td>{{ $parcel->carrier }}</td>
                    <td>{{ $parcel->tracking_id }}</td>
                    <td>{{ $parcel->warehouse_number }} </td>
                    <td>{{ $parcel->customer_reference }} </td>
                    <td>{{ $parcel->corrios_tracking_code }} </td>
                </tr>
                <tr>
                    <th>@lang('orders.invoice.length')</th>
                    <th>@lang('orders.invoice.width')</th>
                    <th>@lang('orders.invoice.height')</th>
                    <th>@lang('orders.invoice.weight')</th>
                    <th colspan="2">@lang('orders.invoice.unit')</th>
                </tr>
                <tr>
                    <td>{{ $parcel->length }} {{ $parcel->is_weight_in_kg ? 'cm' : 'in' }}</td>
                    <td>{{ $parcel->width }} {{ $parcel->is_weight_in_kg ? 'cm' : 'in' }}</td>
                    <td>{{ $parcel->height }} {{ $parcel->is_weight_in_kg ? 'cm' : 'in' }}</td>
                    <td>{{ $parcel->getWeight('kg') }} kg ( {{ $parcel->getWeight('lbs') }} lbs ) </td>
                    <td colspan="2">{{ $parcel->measurement_unit }} </td>
                </tr>
                <tr>
                    <th colspan="6"> Invoice / Fatura </th>
                </tr>
                <tr>
                    <th colspan="6">
                        @if ($parcel->purchaseInvoice)
                        <a href=" {{ $parcel->purchaseInvoice->getPath() }}" target="__blank"> {{ $parcel->purchaseInvoice->name }} </a>
                        @endif
                    </th>
                </tr>
                <tr>
                    <th colspan="6"> Images </th>
                </tr>
                @foreach ($parcel->images as $image)
                <tr>
                    <td>
                        {{ $loop->index+1 }}
                    </td>
                    <td colspan="5">
                        <a href="{{ $image->getPath() }}" target="__blank"> {{ $image->name }} </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
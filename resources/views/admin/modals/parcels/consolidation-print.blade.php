<div class="row" id="consolidation-invoice-form">
    <div class="d-flex justify-content-end w-100 p-4 no-print">
        <button class="btn btn-success">
            <i class="fa fa-print" onclick="print('#consolidation-invoice-form')"></i>
        </button>
    </div>
    <div class="table-responsive-md col-12">
        <table class="table table-bordered">
            <tbody>
                @foreach ($parcel->subOrders as $subOrder)
                    <tr>
                        <td colspan="6">
                            <div class="border-top-success border-2"></div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="6">
                            <div class="h3">
                                Parcel# {{ $loop->index+1 }}
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>@lang('orders.invoice.merchant')</th>
                        <th>@lang('orders.invoice.carrier')</th>
                        <th>@lang('orders.invoice.carrier_tracking')</th>
                        <th>@lang('orders.invoice.whr_number')</th>
                        <th>@lang('orders.invoice.customer_reference')</th>
                        <th>@lang('orders.invoice.tracking_code')</th>
                    </tr>
                    <tr>
                        <td>{{ $subOrder->merchant }}</td>
                        <td>{{ $subOrder->carrier }}</td>
                        <td>{{ $subOrder->tracking_id }}</td>
                        <td>{{ $subOrder->warehouse_number }} </td>
                        <td>{{ $subOrder->customer_reference }} </td>
                        <td>{{ $subOrder->corrios_tracking_code }} </td>
                    </tr>                                
                    <tr>
                        <th>@lang('orders.invoice.length')</th>
                        <th>@lang('orders.invoice.width')</th>
                        <th>@lang('orders.invoice.height')</th>
                        <th>@lang('orders.invoice.weight')</th>
                        <th colspan="2">@lang('orders.invoice.unit')</th>
                    </tr>
                    <tr>
                        <td>{{ $subOrder->length }} {{ $subOrder->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                        <td>{{ $subOrder->width }} {{ $subOrder->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                        <td>{{ $subOrder->height }} {{ $subOrder->isMeasurmentUnitCm() ? 'cm' : 'in' }}</td>
                        <td>{{ $subOrder->getWeight('kg') }} kg ( {{ $subOrder->getWeight('lbs') }} lbs ) </td>
                        <td colspan="2">{{ $subOrder->measurement_unit }} </td>
                    </tr>      
                @endforeach
                <tr>
                    <th colspan="6">
                        <div class="h3">
                            Additional Services / Serviços adicionais
                        </div>
                    </th>
                </tr>
                @foreach ($parcel->services as $service)
                    <tr>
                        <td>
                            {{ $loop->index+1 }}
                        </td>
                        <td colspan="3">{{ $service->name }}</td>
                        <td colspan="2">{{ $service->price }} USD</td>
                    </tr>
                @endforeach           
            </tbody>
        </table>
    </div>
</div>
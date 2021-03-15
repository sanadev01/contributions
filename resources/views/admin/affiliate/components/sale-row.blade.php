
<tr>
    <td>
        <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
            <input type="checkbox" name="sales[]" class="bulk-sales" value="{{$sale->id}}">
            <span class="vs-checkbox vs-checkbox-lg">
                <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                </span>
            </span>
            <span class="h3 mx-2 text-primary my-0 py-0"></span>
        </div>
    </td>
    <td>
        {{ optional($sale->created_at)->format('m/d/Y') }}
    </td>
    @admin
        <td>
            {{ $sale->user->name }}
        </td>
    @endadmin
    <td>
        {{ $sale->order->user->name }}
    </td>
    <td>
        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$sale->order) }}" class="btn btn-primary" title="@lang('sales-commission.Show Order Details')">
            <i class="feather icon-list"></i> @lang('sales-commission.view-order')
        </button>
        
    </td>
    <td>
        {{ $sale->order->warehouse_number }}
    </td>
    <td>
        {{ $sale->order->customer_reference }}
    </td>
    <td>
        {{ $sale->order->tracking_id }}
    </td>
    <td>
        {{ $sale->order->weight . $sale->order->measurement_unit }}
    </td>
    
    <td>
        {{ $sale->value }}
        
    </td>
    <td>
        {{ $sale->type }}
        
    </td>
    <td>
        {{ $sale->commission? number_format($sale->commission, 2): 0 }}
    </td>
    <td>
        @if( $sale->is_paid )
            <i class="feather icon-check text-success"></i>
        @else
            <i class="feather icon-x text-danger"></i>
        @endif
    </td>
    <td>
        <span class="btn btn-sm btn-success">
                {{ $sale->detail }}
        </span>
    </td>
    
</tr>

<tr>
    @admin
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
    @endadmin
    <td>
        {{ optional($sale->created_at)->format('m/d/Y') }}
    </td>
    @admin
        <td>
            {{ $sale->user->name }}
        </td>
    @endadmin
    <td>
        {{ optional($sale->order->user)->name }}
    </td>
    @admin
        <td>
            <a href="#" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$sale->order) }}" title="@lang('sales-commission.Show Order Details')">
                @lang('sales-commission.view-order')
            </a>
            
        </td>
    @endadmin
    <td>
        {{ $sale->order->warehouse_number }}
    </td>
    <td>
        {{ $sale->order->corrios_tracking_code }}
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
        {{ number_format($sale->value, 2) }}
        
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
    {{-- <td>
        <span class="btn-success">
                {{ $sale->detail }}
        </span>
    </td> --}}
    @admin
        <td class="d-flex">
            <div class="btn-group">
                <div class="dropdown">
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('parcel.Action')
                    </button>
                    <div class="dropdown-menu dropdown-menu-right dropright">
                        @can('delete', $sale)
                            <form method="post" action="{{ route('admin.affiliate.sales-commission.destroy',$sale) }}" class="d-inline-block w-100" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button class="dropdown-item w-100 text-danger" title="@lang('parcel.Delete Parcel')">
                                    <i class="feather icon-trash-2"></i> @lang('parcel.Delete')
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </td>
    @endadmin 
</tr>
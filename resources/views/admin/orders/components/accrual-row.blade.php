<tr @if( $order->user->hasRole('retailer') &&  !$order->isPaid()) class="bg-danger text-white" @endif>
 
    <td>
        
        {{ optional($order->order_date)->format('m/d/Y') }}
    </td>
    <td style="width: 200px;">
        @if ( $order->is_arrived_at_warehouse )
            <i class="fa fa-star text-success p-1"></i>
        @endif
        @if( $order->warehouse_number)
            <span>
                <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$order->encrypted_id) }}">
                     {{ $order->warehouse_number }}
                </a>
            </span>
        @endif 
        <span title="Consolidation Requested For Following Shipments">
            @foreach( $order->subOrders as $subOrder)
                <a href="#" class="mb-1 d-block" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$subOrder->encrypted_id) }}">
                    WHR#: {{ $subOrder->warehouse_number }}
                </a>
            @endforeach
        </span>
    </td>
    @admin
    <td>
        {{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}
    </td>
    @endadmin  
    <td>
        {{ $order->carrier }}
    </td> 
    <td>
        {{ $order->corrios_tracking_code }}
        @if($order->has_second_label)
            <hr>
            {{ $order->us_api_tracking_code }}
        @endif
    </td>
    <td>
        ${{ number_format($order->gross_total,2) }}
    </td> 
    <td>
        ${{ number_format($order->tax_and_duty,2) }}
    </td> 
    <td>
        ${{ number_format($order->fee_for_tax_and_duty,2) }}
    </td> 
  
    <td class="font-large-1">
        @if( $order->isPaid() )
            <i class="feather icon-check text-success"></i>
        @else
            <i class="feather icon-x  @if( $order->user->hasRole('retailer') &&  !$order->isPaid()) text-white @else text-danger @endif"></i>
        @endif
    </td>
</tr>
<tr>
    <td>
        {{ $parcel->created_at->format('m/d/y') }}
    </td>
    @admin
        <td>{{ optional($parcel->user)->name }}</td>
        <td>{{ optional($parcel->user)->pobox_number }}</td>
    @endadmin
    @if($parcel->isShipmentAdded())
        <td>
            <a href="#" title="@lang('parcel.Show Details')">
                {{ $parcel->warehouse_number }}
            </a>
        </td>
    @else
        <td>
            @lang('parcel.Not Created Yet')
        </td>
    @endif
    <td>
        @if($parcel->isShipmentAdded())
            {{ number_format($parcel->getOriginalWeight('kg'),2) }} kg <hr>
            {{ number_format($parcel->getOriginalWeight('lbs'),2) }} lbs
        @else
        @lang('parcel.Un Available')
        @endif
    </td>
    <td>
        @if($parcel->isShipmentAdded())
            {{ number_format($parcel->getWeight('kg'),2) }} kg <hr>
            {{ number_format($parcel->getWeight('lbs'),2) }} lbs
        @else
        @lang('parcel.Un Available')
        @endif
    </td>
    <td>{{ $parcel->merchant }}</td>
    <td>
        {{ $parcel->carrier }}
    </td>
    <td class="p-1">
        {{ $parcel->tracking_id }}
    </td>
    <td>
        @if( $parcel->isShipmentAdded() )
            <span class="btn btn-sm btn-primary" title="@lang('parcel.Shipment Is Ready Please Click on basket icon to Proceed to Order')">@lang('parcel.Ready') </span>
        @else
            <span class="btn btn-sm btn-danger">@lang('parcel.Transit')</span>
        @endif
    </td>
    <td class="d-flex">
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('parcel.Action')
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">

                    @if( $parcel->isShipmentAdded() )
                        <a href="{{ route('admin.orders.sender.index',$parcel) }}" class="dropdown-item" title=" @lang('parcel.Create Order')">
                            <i class="feather icon-shopping-cart"></i> @lang('prealerts.actions.place-order')
                        </a>
                    @endif
                    
                    @can('update',  $parcel)
                        <a href="{{ route('admin.parcels.edit',$parcel) }}" class="dropdown-item btn" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                        </a>
                    @endcan
                    @can('delete', $parcel)
                        <form method="post" action="{{ route('admin.parcels.destroy',$parcel) }}" class="d-inline-block w-100" onsubmit="return confirmDelete()">
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
</tr>
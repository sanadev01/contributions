<tr>
    <td>
        {{ $prealert->created_at->format('m/d/y') }}
    </td>
    @admin
        <td>{{ $prealert->user->name }}</td>
        <td>{{ $prealert->user->pobox_number }}</td>
    @endadmin
    @if($prealert->isShipmentAdded())
        <td>
            <a href="#" title="Show Details">
                {{ $prealert->warehouse_number }}
            </a>
        </td>
    @else
        <td>
            Not Created Yet
        </td>
    @endif
    <td>
        @if($prealert->shipment)
            {{ number_format($prealert->shipment->getGrossWeight(),2) }} {{ $prealert->shipment->getWeightUnit() }} <hr>
            {{ $prealert->shipment->getWeightInOtherUnit() }}
        @else
            Un Available
        @endif
    </td>
    <td>
        @if($prealert->shipment)
            {{ number_format($prealert->shipment->getVolumetricWeight(),2) }} {{ $prealert->shipment->getWeightUnit() }} <hr>
            {{ $prealert->shipment->getVolumeWeightInOtherUnit() }}
        @else
            Un Available
        @endif
    </td>
    <td>{{ $prealert->merchant }}</td>
    <td>
        {{ $prealert->carrier }}
    </td>
    <td class="p-1">
        {{ $prealert->tracking_id }}
    </td>
    <td>
        @if( $prealert->isShipmentAdded() )
            <span class="btn btn-sm btn-primary" title="Shipment Is Ready Please Click on basket icon to Proceed to Order">Ready </span>
        @else
            <span class="btn btn-sm btn-danger">Transit</span>
        @endif
    </td>
    <td class="d-flex" style="zoom: 1.2">
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">
                    @user
                        @if( $prealert->shipment )
                            <a href="{{ route('admin.shipments.orders.create',$prealert->shipment) }}" class="dropdown-item" title="Create Order">
                                <i class="feather icon-shopping-cart"></i> @lang('prealerts.actions.place-order')
                            </a>
                        @endif
                    @enduser
                    @if( $prealert->shipment && auth()->user()->can('update', $prealert->shipment) )
                        <a href="{{ route('admin.parcels.shipments.edit',[ $prealert, $prealert->shipment ]) }}" class="dropdown-item btn p-1" title="Edit Shipment Details">
                            <i class="feather icon-package"></i> Edit Shipment
                        </a>
                    @elseif( auth()->user()->can('create', \App\Models\Shipment::class) )
                        <a href="{{ route('admin.parcels.shipments.create',$prealert) }}" class="dropdown-item btn p-1 " title="Add Shipment Details">
                            <i class="feather icon-package"></i> Create Shipment
                        </a>
                    @endif
                    <a href="{{ route('admin.parcels.show',$prealert) }}" class="dropdown-item btn p-1" title="Show Details" wire:click.prevent="$emit('showModal','showPrealertDetailModal',{{$prealert->id}})">
                        <i class="feather icon-list"></i> Details
                    </a>

                    @can('update',  $prealert)
                        <a href="{{ route('admin.parcels.edit',$prealert) }}" class="dropdown-item btn p-1" title="Edit PreAlert">
                            <i class="feather icon-edit"></i> Edit Prealert
                        </a>
                    @endcan
                    @can('delete', $prealert)
                        <form method="post" action="{{ route('admin.parcels.destroy',$prealert) }}" class="d-inline-block w-100" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button class="dropdown-item p-1 w-100 text-danger" title="Delete Pre Alert">
                                <i class="feather icon-trash-2"></i> Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </td>
</tr>
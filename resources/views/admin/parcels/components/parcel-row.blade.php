<tr>
    <td>
        {{ $parcel->created_at->format('m/d/y') }}
    </td>
    @admin
        <td>
            <div class="media-body valign-middle" id="imageDecrptionTopParcel" style="width:175px; font-size:15px !important">
                <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal"
                    data-url="{{ route('admin.modals.parcel.shipment-info', $parcel) }}">
                    {{ $parcel->user->name }}
                    {{-- - {{ $parcel->user->hasRole('wholesale') ? 'W' : 'R' }} --}}
                </a>
            </div>

            <div id="imageDecrptionParcel" style="font-size: 14px !important; width: 175px;">
                {{-- <span id="tracking"> --}}
                @if ($parcel->is_consolidated)
                    <span>
                        Consolidated
                    </span>
                @else
                    <span>
                        Non-Consolidated
                    </span>
                @endif
                {{-- </span> --}}
            </div>
            {{-- {{ optional($parcel->user)->name }} --}}
        </td>
        <td class="col-2">{{ optional($parcel->user)->pobox_number }}</td>
    @endadmin
    <td>
        @if ($parcel->isArrivedAtWarehouse())
            <i class="fa fa-star text-success p-1"></i>
        @endif
        @if ($parcel->isShipmentAdded())
            <a href="#" class="d-block" title="@lang('parcel.Show Details')" data-toggle="modal" data-target="#hd-modal"
                data-url="{{ route('admin.modals.parcel.shipment-info', $parcel) }}">
                {{ $parcel->warehouse_number }}
            </a>
        @else
            @lang('parcel.Not Created Yet')
        @endif
        @if ($parcel->isConsolidated())
            <hr>
            @foreach ($parcel->subOrders as $subParcel)
                <a href="#" class="d-block" title="@lang('parcel.Show Details')" data-toggle="modal" data-target="#hd-modal"
                    data-url="{{ route('admin.modals.parcel.shipment-info', $subParcel) }}">
                    {{ $subParcel->warehouse_number }}
                </a>
            @endforeach
        @endif
    </td>
    <td>
        @if ($parcel->isShipmentAdded())
            {{ number_format($parcel->getOriginalWeight('kg'), 2) }} kg
            <hr>
            {{ number_format($parcel->getOriginalWeight('lbs'), 2) }} lbs
        @else
            @lang('parcel.Un Available')
        @endif
    </td>
    <td>
        @if ($parcel->isShipmentAdded())
            {{ number_format($parcel->getWeight('kg'), 2) }} kg
            <hr>
            {{ number_format($parcel->getWeight('lbs'), 2) }} lbs
        @else
            @lang('parcel.Un Available')
        @endif
    </td>
    {{-- <td>{{ $parcel->merchant }}</td> --}}
    {{-- <td>
        {{ $parcel->carrier }}
    </td> --}}
    <td class="p-1">
        {{ $parcel->tracking_id }}
    </td>
    <td class="text-center">
        @if (!$parcel->isConsolidated() && $parcel->isShipmentAdded())
            <span class="btn col-9 btn-sm btn-primary" title="@lang('parcel.Shipment Is Ready Please Click on basket icon to Proceed to Order')">@lang('parcel.Ready') </span>
        @elseif($parcel->isConsolidated() && !$parcel->isShipmentAdded())
            <span class="col-12 btn btn-sm btn-info">@lang('consolidation.Consolidation Requested')</span>
        @elseif($parcel->isConsolidated() && $parcel->isShipmentAdded())
            <span class="btn col-10 btn-sm btn-warning">@lang('consolidation.Consolidated')</span>
        @else
            <span class="btn col-10 btn-sm btn-danger">@lang('parcel.Transit')</span>
        @endif
    </td>
    <td>
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" style="width:100px;"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('parcel.Action')
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">

                    @if ($parcel->isShipmentAdded())
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.orders.sender.index', $parcel) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="dropdown-item" title=" @lang('parcel.Create Order')">
                            <i class="feather icon-shopping-cart"></i> @lang('prealerts.actions.place-order')
                        </a>
                    @endif

                    @if (auth()->user()->can('canPrintConsolidationForm', $parcel) &&
                        $parcel->isConsolidated() &&
                        Auth::user()->isActive())
                        <a href="#" class="dropdown-item btn" title="@lang('consolidation.Print Consolidation Request')" data-toggle="modal"
                            data-target="#hd-modal"
                            data-url="{{ route('admin.modals.parcel.consolidation-print', $parcel) }}">
                            <i class="fa fa-print"></i> @lang('consolidation.Print Consolidation Request')
                        </a>
                    @endif
                    @can('update', $parcel)
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.parcels.edit', $parcel) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="dropdown-item btn" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                        </a>
                    @endcan
                    @can('duplicatePreAlert', $parcel)
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.parcel.duplicate', $parcel) }}"  @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="dropdown-item btn" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Duplicate Parcel')
                        </a>
                    @endcan
                    @if (auth()->user()->can('updateConsolidation', $parcel) && $parcel->isConsolidated())
                        <a @if (Auth::user()->isActive()) href="{{ route('admin.consolidation.parcels.edit', $parcel) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                            class="dropdown-item btn" title="@lang('consolidation.Edit Consolidation')">
                            <i class="feather icon-edit"></i> @lang('consolidation.Edit Consolidation')
                        </a>
                    @endif
                    @if (Auth::user()->isActive())
                        @can('delete', $parcel)
                            <form method="post" action="{{ route('admin.parcels.destroy', $parcel) }}"
                                class="d-inline-block w-100" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button class="dropdown-item w-100 text-danger" title="@lang('parcel.Delete Parcel')">
                                    <i class="feather icon-trash-2"></i> @lang('parcel.Delete')
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </td>
</tr>

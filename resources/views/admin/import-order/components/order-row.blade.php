<tr>
    <td>{{optional($order->created_at)->format('m/d/Y')}}</td>
    @admin
        <td>{{$order->user->name}}</td>
    @endadmin
    <td>{{$order->merchant}}</td>
    <td>{{$order->carrier}}</td>
    <td>{{$order->customer_reference}}</td>
    <td>{{$order->tracking_id}}</td>
    <td>
        @if($order->error)
            <a href="#" title="Click to see error" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.error',$order) }}">
                See Error
            </a>
        @endif
    </td>
    <td class="d-flex no-print" >
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('orders.actions.actions')
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">
                    @if(!$order->error)
                        <a href="{{ route('admin.import.import-order.edit',$order->id) }}" class="btn dropdown-item w-100" title="Move to Order">
                            <i class="feather icon-arrow-right-circle"></i> Move to Order
                        </a>
                   @else
                        <a wire:click="$emit('edit-order',{{$order->id}})" class="btn dropdown-item w-100 edit-order" title="Fix Error">
                            <i class="feather icon-alert-circle"></i> Fix Error
                        </a>
                    @endif
                   <form action="{{ route('admin.import.import-order.destroy',$order->id) }}" method="post" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button class="btn dropdown-item w-100 text-danger" title="Delete Record">
                            <i class="feather icon-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td>{{optional($order->created_at)->format('m/d/Y')}}</td>
    @admin
        <td>{{$order->user->name}}</td>
    @endadmin
    <td>{{$order->file_name}}</td>
    <td>
        <a href="{{ route('admin.import.import-excel.show',[$order->id, 'type'=>'good']) }}" type="button" class="btn btn-success btn-sm">
            Pass: {{ $order->importOrders->where('error', null)->count() }}
        </a>
        
        <a href="{{ route('admin.import.import-excel.show',[$order->id, 'type'=>'error']) }}" type="button" class="btn btn-danger btn-sm">
            Error: {{ $order->importOrders->where('error', '!=', null)->count() }}
        </a>
    
    </td>
    <td class="d-flex no-print" >
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('orders.actions.actions')
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">

                    <a href="{{ route('admin.import.import-excel.show',$order->id) }}"  class="btn dropdown-item w-100" title="View Orders">
                        <i class="feather icon-eye"></i> View Orders
                    </a>

                   <form action="{{ route('admin.import.import-excel.destroy',$order->id) }}" method="post" onsubmit="return confirmDelete()">
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
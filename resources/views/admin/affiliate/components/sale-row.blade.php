
<tr>
    <td>
        {{ optional($sale->created_at)->format('m/d/Y') }}
    </td>
    @admin
        <td>
            {{ $sale->user->name }}
        </td>
    @endadmin
    <td>
        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$sale->order) }}" class="btn btn-primary" title="@lang('sales-commission.Show Order Details')">
            <i class="feather icon-list"></i> @lang('sales-commission.view-order')
        </button>
        
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
    
</tr>
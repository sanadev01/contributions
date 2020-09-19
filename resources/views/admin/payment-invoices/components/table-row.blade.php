<tr>
    <td>{{ $invoice->uuid }}</td>
    @admin
    <td>{{ optional($invoice->user)->name }}</td>
    @endadmin
    <td>
        {{ $invoice->order_count }}
    </td>
    <td>
        {{ $invoice->total_amount }} 
    </td>
    <td>
        {{ $invoice->last_four_digits  }}
    </td>

    <td>
        @if ( $invoice->isPaid() )
            <span class="btn btn-sm btn-success">
                Paid
            </span>
        @else
            <span class="btn btn-sm btn-danger">
                Pending
            </span>
        @endif
    </td>
    
    <td>
        @if ( auth()->user()->can('update',$invoice) && !$invoice->isPaid())
            <a href="{{ route('admin.payment-invoices.invoice.edit',$invoice) }}" title="Edit Invoice" class="btn btn-sm btn-primary mr-2">
                <i class="feather icon-edit"></i>
            </a>
        @endif

        @if ( auth()->user()->can('view',$invoice) && !$invoice->isPaid())
            <a href="{{ route('admin.payment-invoices.invoice.checkout.index',$invoice) }}" title="Pay Invoice" class="btn btn-sm btn-primary mr-2">
                <i class="feather icon-dollar-sign"></i>
            </a>
        @endif

        @can('view', $invoice)
            <a href="{{ route('admin.payment-invoices.invoice.show',$invoice) }}" title="View Invoice" class="btn btn-sm btn-primary mr-2">
                <i class="feather icon-eye"></i>
            </a>
        @endcan

        @if ( auth()->user()->can('delete',$invoice) && !$invoice->isPaid())
            <form action="{{ route('admin.payment-invoices.destroy',$invoice) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                @csrf
                @method('DELETE')
                <button title="Delete Invoice" class="btn btn-sm btn-danger mr-2">
                    <i class="feather icon-trash"></i>
                </button>
            </form>
        @endif
    </td>
</tr>
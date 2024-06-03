<tr>
    <td>{{ $invoice->uuid }}</td>
    @admin
    <td>{{ optional($invoice->user)->name }}</td>
    @endadmin
    <td>
        {{ round($invoice->orders()->count(),2) }}
    </td>
    <td>
        {{ number_format($invoice->orders()->sum('gross_total'),2) }} USD
    </td>
    <td>
        @if ( $invoice->isPaid() )
        @if ($invoice->last_four_digits)
        {{ $invoice->last_four_digits  }}
        @else
        @lang('invoice.Paid with Balance')
        @endif
        @endif
    </td>

    <td>
        <a href="{{ auth()->user()->can('canChangeStatus',$invoice) ? route('admin.payment-invoices.paid.toggle',$invoice):'#' }}" title="{{ auth()->user()->can('canChangeStatus',$invoice) ? 'Click to Toggle Status': '' }}">
            @if ( $invoice->isPaid() )

            <span class="btn btn-sm btn-success">
                @lang('invoice.Paid')
            </span>
            @else
            <span class="btn btn-sm btn-danger">
                @lang('invoice.Pending')
            </span>
            @endif
        </a>
    </td>

    <td>
        <a href="{{ auth()->user()->can('canChnageType',$invoice) ? route('admin.payment-invoices.type.toggle',$invoice):'#' }}" title="{{ auth()->user()->can('canChangeStatus',$invoice) ? 'Click to Toggle Status': '' }}">
            @if ( $invoice->isPrePaid() )
            <span class="btn btn-sm btn-success">
                @lang('invoice.PrePaid')
            </span>
            @else
            <span class="btn btn-sm btn-warning">
                @lang('invoice.PostPaid')
            </span>
            @endif
        </a>
    </td>

    <td>
        {{ optional($invoice->created_at)->format('m/d/Y') }}
    </td>

    <td>
        @if ( auth()->user()->can('update',$invoice) && !$invoice->isPaid())
        <a href="{{ route('admin.payment-invoices.invoice.edit',$invoice) }}" title="@lang('invoice.Edit Invoice')" class="btn btn-sm btn-primary mr-2">
            <i class="feather icon-edit"></i>
        </a>
        @endif

        @if ( auth()->user()->can('view',$invoice) && !$invoice->isPaid())
        <a href="{{ route('admin.payment-invoices.invoice.checkout.index',$invoice) }}" title="@lang('invoice.Pay Invoice')" class="btn btn-sm btn-primary mr-2">
            <i class="feather icon-dollar-sign"></i>
        </a>
        @endif

        @can('view', $invoice)
        <a href="{{ route('admin.payment-invoices.invoice.show',$invoice) }}" title="@lang('invoice.View Invoice')" class="btn btn-sm btn-primary mr-2">
            <i class="feather icon-eye"></i>
        </a>
        @endcan

        @if ( auth()->user()->can('delete',$invoice) && !$invoice->isPaid())
        <form action="{{ route('admin.payment-invoices.destroy',$invoice) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
            @csrf
            @method('DELETE')
            <button title="@lang('invoice.Delete Invoice')" class="btn btn-sm btn-danger mr-2">
                <i class="feather icon-trash"></i>
            </button>
        </form>
        @endif
    </td>
</tr>
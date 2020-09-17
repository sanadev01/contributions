@extends('layouts.master') 

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Payment Invoices
                        </h4>
                        <a href="{{ route('admin.payment-invoices.orders.index') }}" class="btn btn-primary">
                            Create Invoice
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        @admin
                                        <th>User</th>
                                        @endadmin
                                        <th>Orders Count</th>
                                        <th>Amount</th>
                                        <th>Card Last 4 Digits</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
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
                                                @can('update', $invoice)
                                                    <a href="{{ route('admin.payment-invoices.invoice.edit',$invoice) }}" title="Edit Invoice" class="btn btn-sm btn-primary mr-2">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('view', $invoice)
                                                    <a href="{{ route('admin.payment-invoices.invoice.show',$invoice) }}" title="View Invoice" class="btn btn-sm btn-primary mr-2">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $invoice)
                                                    <form action="{{ route('admin.payment-invoices.destroy',$invoice) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button title="Delete Invoice" class="btn btn-sm btn-danger mr-2">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $invoices->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

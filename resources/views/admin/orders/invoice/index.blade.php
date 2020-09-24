@extends('admin.orders.layouts.wizard')

@section('wizard-form')
    <section class="invoice-print mb-1">
        <div wire:id="MhlECvHN71T5Xdylc5Vi">
      
            <div class="row">
                <fieldset class="col-12 col-md-5 mb-1 mb-md-0">

                </fieldset>
            </div>
        </div>
    </section>
    <div  class="wizard print">
        <!-- invoice functionality end -->
        <!-- invoice page --> 
        @include('admin.modals.orders.invoice')
        <div class="actions clearfix no-print">
            <ul role="menu" aria-label="Pagination">
                @if ( !$order->isPaid() )
                    <li class="disabled" aria-disabled="true">
                        <a href="{{ route('admin.orders.order-details.index',$order) }}" role="menuitem">@lang('orders.invoice.Previous')</a>
                    </li>
                    <li aria-hidden="false" aria-disabled="false">
                        <a href="{{ route('admin.payment-invoices.orders.index',['order'=>$order]) }}" class="btn btn-primary">Pay Order Now</a>
                    </li>
                @else
                <li aria-hidden="false" aria-disabled="false">
                    <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">Back To List</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
@endsection

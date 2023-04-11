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
                <li class="disabled" aria-disabled="true">
                    @if (($order->user->hasRole('wholesale') && $order->user->insurance == false) || ($order->user->hasRole('retailer')))
                        <a href="{{ route('admin.orders.services.index',$order->encrypted_id) }}" role="menuitem">@lang('orders.invoice.Previous')</a>
                    @else
                        <a href="{{ route('admin.orders.order-details.index',$order->encrypted_id) }}" role="menuitem">@lang('orders.invoice.Previous')</a>
                    @endif
                </li>
                @if ( !$order->isPaid() )
                <li aria-hidden="false" aria-disabled="false">
                    <a href="{{ route('admin.payment-invoices.orders.index',['order'=>$order]) }}" class="btn btn-primary">Pay Order Now</a>
                </li>
                @else
                <li aria-hidden="false" aria-disabled="false">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Back to List / De volta à lista</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
@endsection

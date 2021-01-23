<section class="card invoice-page border border-danger">
    <div class="col-12 row text-danger">
        <h2>@lang('orders.import-excel.Order Errors')</h2>
    </div>
    @foreach ($order->error as $item)
     
        <li class="text-danger">{{ $item }}</li>
            
    @endforeach

</section>


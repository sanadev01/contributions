<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" onclick="reloadLabel({{$order->id}},'#row_{{$order->id}}')">Reload</button>
        @if (!$error)
        
        @if($order->shippingService->is_total_express)
        
        <button onclick="window.open('@if($order->shippingService->is_total_express) {{ $order->totalExpressLabelUrl() }} @else{{ route('order.label.download',[encrypt($order->id),'time'=>md5(microtime())]) }}@endif','','top:0,left:0,width:600px;height:700px;')" class="btn btn-primary">Download</button>
        
        @else
            <button onclick="window.open('@if($order->shippingService->isColombiaService()) {{ $order->colombiaLabelUrl() }} @else{{ route('order.label.download',[encrypt($order->id),'time'=>md5(microtime())]) }}@endif','','top:0,left:0,width:600px;height:700px;')" class="btn btn-primary">Download</button>
         @endif
            <!-- <button class="btn btn-primary" onclick="updateLabel({{$order->id}},'#row_{{$order->id}}')">Update</button> -->
            @can('canPrintLableUpdate', $order)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirm">Update</button>
            @endcan
                
        @endif
    </div>
</div>

@if (!$buttonsOnly)
    <div class="label mt-2">
        @if (!$error)
            <!--
                Sinerlog modification
                Shows the sinerlog label
             -->
             @if ($order->sinerlog_tran_id != '')
            <iframe src="" style="width:100%; height:700px;" frameborder="0">
                
            </iframe>
            @else
            <iframe src="https://docs.google.com/gview?url={{ route('order.label.download', encrypt($order->id)) }}&embedded=true&time{{md5(microtime())}}" style="width:100%; height:700px;" frameborder="0">
                <iframe src="{{ route('order.label.download', encrypt($order->id)) }}" style="width:100%; height:700px;" frameborder="0"></iframe>
            </iframe>
            @endif
        @else
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif
    </div>
@endif
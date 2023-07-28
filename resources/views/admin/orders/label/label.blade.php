<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" onclick="reloadLabel({{$order->id}},'#row_{{$order->id}}')">Reload</button>
        @if (!$error)
            <button onclick="window.open('{{ route('order.label.download',[ encrypt($order->id) ,'time'=>md5(microtime())]) }}','','top:0,left:0,width:600px;height:700px;')" class="btn btn-primary">Download</button>
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
                <iframe src="@isset($renderLabel) {{$renderLabel}} @endisset" style="width:100%; height:700px;" frameborder="0">
                
                </iframe>
            @else
                <iframe src="https://docs.google.com/gview?url={{ route('order.label.download', encrypt($order->id)) }}&embedded=true&time{{md5(microtime())}}" style="width:100%; height:700px;" frameborder="0">
                    <iframe src="{{ route('order.label.download', encrypt($order->id)) }}" style="width:100%; height:700px;" frameborder="0"></iframe>
                </iframe>
            @endif
        @else
            <div class="alert alert-danger">
                <ul> 
                    @foreach(explode('<br>',$error) as $e)
                    <li>{{ $e}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
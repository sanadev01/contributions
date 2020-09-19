<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" onclick="reloadLabel()">Reload</button>
        @if (!$error)
            <a href="{{ route('order.label.download',$order) }}" target="_blank" class="btn btn-primary">Download</a>
            <button class="btn btn-primary" onload="updateLabel()">Update</button>
        @endif
    </div>
</div>
<div class="label mt-2">
    @if (!$error)
        <iframe src="http://docs.google.com/gview?url={{ route('order.label.download',$order) }}&embedded=true" style="width:100%; height:700px;" frameborder="0"></iframe>
    @else
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @endif
</div>
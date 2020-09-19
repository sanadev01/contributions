<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary" onclick="reloadLabel()">Reload</button>
        @if (!$error)
            <button class="btn btn-primary" {{ route('order.label.download',$order) }}>Download</button>
            <button class="btn btn-primary">Update</button>
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
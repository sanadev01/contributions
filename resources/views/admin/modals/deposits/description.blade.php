<div class="modal-header">
    <h5 class="modal-title">Description</h5>
</div>
<div class="modal-body">
    <form action="{{ route('admin.deposit.description.update',$deposit) }}" method="POST">
        @csrf
        <textarea class="form-control" rows="5" name="description">{{ $deposit->description }}</textarea>
        {{-- <p>{{ $deposit->description }}</p> --}}
        <button class="btn btn-primary mt-2">Update</button>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

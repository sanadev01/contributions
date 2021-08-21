<div class="modal-header">
    <h5 class="modal-title">Description</h5>
</div>
<div class="modal-body">
    @user
    <p>{{ $deposit->description }}</p>
    @enduser
    @admin
    <form action="{{ route('admin.deposit.description.update',$deposit) }}" method="POST">
        @csrf
        <textarea class="form-control" rows="5" name="description">{{ $deposit->description }}</textarea>
        <button class="btn btn-primary mt-2">Update</button>
    </form>
    @endadmin
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

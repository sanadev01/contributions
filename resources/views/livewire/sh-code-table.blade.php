<div>
    <input class="form-control col-6 mx-3 my-3" type="text" wire:model="search" placeholder="Search...">
    
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Code</th>
                <th>English</th>
                <th>Portuguese</th>
                <th>Spanish</th>
                <th>Type</th>
                <th>@lang('role.Action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shCodes as $shCode)
                <tr>
                    <td>{{ $shCode->code }}</td>
                    <td>{{ optional(explode('-------', $shCode->description))[0] }}</td>
                    <td>{{ optional(explode('-------', $shCode->description))[1] }}</td>
                    <td>{{ optional(explode('-------', $shCode->description))[2] }}</td>
                    <td>{{ $shCode->type }}</td>
                    <td class="d-flex">
                        <a href="{{ route('admin.shcode.edit', $shCode) }}" class="btn btn-primary mr-2" title="Edit Shcode">
                            <i class="feather icon-edit"></i>
                        </a>

                        <form action="{{ route('admin.shcode.destroy', $shCode) }}" method="post" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" title="Delete Shcode">
                                <i class="feather icon-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $shCodes->links() }}
</div>

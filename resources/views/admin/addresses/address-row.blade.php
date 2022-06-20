<tr>
    <td>
        {{ $address->user->name .' '. $address->user->last_name }}
    </td>
    <td>{{ $address->first_name }} {{ $address->last_name }}</td>
    <td>{{ $address->address }}</td>
    <td>{{ $address->address2 }}</td>
    <td>{{ $address->street_no }}</td>
    <td>
        {{ $address->country->name }}
    </td>
    <td>
        {{ $address->city }}
    </td>
    <td>
        {{ $address->state->code ?? '' }}
    </td>
    <td> 
        @if ( $address->account_type == 'individual' )
            {{ $address->tax_id }}
        @endif
    </td>
    <td>
        @if ( $address->account_type == 'business' )
            {{ $address->tax_id }}
        @endif
    </td>
    
    <td>
        {{ $address->phone }}
    </td>
    <td class="d-flex">
        @can('update', $address)
            <a href="{{ route('admin.addresses.edit',$address->id) }}" class="btn btn-primary mr-2" title="@lang('address.Edit Address')">
                <i class="feather icon-edit"></i>
            </a>
        @endcan

        @can('delete', $address)
            <form action="{{ route('admin.addresses.destroy',$address->id) }}" method="post" onsubmit="return confirmDelete()">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" title="@lang('address.Delete Address')">
                    <i class="feather icon-trash"></i>
                </button>
            </form>
        @endcan
    </td>
</tr>
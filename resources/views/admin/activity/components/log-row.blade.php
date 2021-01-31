
<tr>
    <td>
        {{ optional($activity->created_at)->format('m/d/Y') }}
    </td>

    <td>
        <strong>Username: </strong> {{ optional($activity->causer)->name }}<br> 
        <strong>Description: </strong> {{ optional($activity)->description }}<br>
        <strong>Model: </strong> {{ optional($activity)->subject_type }}
        <div class="old p-3">
            <h4> Values After Change</h4>
            <hr>
            @foreach ( optional($activity->changes)['attributes'] ? optional($activity->properties)['attributes'] : [] as $key =>  $value)
                <span class="mx-3"> 
                    @if(!is_array($value))<span class="key text-success"> {{ $key }}</span> => {{ $value }} @endif
                </span>
            @endforeach
        </div>
        <div class="old p-3 mt-3">
            <h4> Values before Change</h4>
            <hr>
            @foreach ( optional($activity->changes)['old'] ? optional($activity->properties)['old'] : [] as $key =>  $value)
                <span class="mx-3">
                    @if(!is_array($value))<span class="key text-danger"> {{ $key }}</span> => {{ $value }} @endif 
                </span>
            @endforeach
        </div>
    </td>
</tr>


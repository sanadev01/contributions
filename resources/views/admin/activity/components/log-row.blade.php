
<tr>
    <td>
        {{ optional($activity->created_at)->format('m/d/Y') }}
    </td>

    <td>
        <strong>Username: </strong> {{ optional($activity->causer)->name }}, 
        <strong>Description: </strong> {{ optional($activity)->description }},
        <strong>Model: </strong> {{ optional($activity)->subject_type }}
        @if( optional($activity)->description == 'updated')
            <h4>old value</h4>
            <div class="d-flex">
                @foreach ( optional($activity->changes)['old'] ? optional($activity->properties)['old'] : [] as $key =>  $item)
                <div class="column-flex">
                    <div class="pr-2" style="width: 100px;">
                        <strong>{{ $key }}</strong>
                    </div>
                    <div class="pr-2" style="width: 100px;">
                        {{ $item }}
                    </div>
                </div>
                @endforeach
            </div>
            <hr>
        @endif
        <h4>{{ optional($activity)->description == 'deleted' ? 'Deleted Value' : 'New Value'}}</h4>
        <div class="d-flex">
            @foreach ( optional($activity)->changes['attributes'] as $key => $item)
            <div class="column-flex">
                @if($activity->description != 'updated')
                <div class="pr-2" style="width: 100px;">
                    <strong>{{ $key }}</strong>
                </div>
                @endif
                <div class="pr-2" style="width: 100px;">
                    {{ $item }}
                </div>
            </div>
            @endforeach
        </div>
    </td>
</tr>
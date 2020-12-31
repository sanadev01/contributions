
<tr>
    <td>
        {{ optional($activity->created_at)->format('m/d/Y') }}
    </td>
    <td>
        {{ $activity->causer->name }}
    </td>
    <td>
        {{ $activity->description }}
    </td>

    <td>
        @if($activity->description == 'updated')
            <h4>old value</h4>
            <div class="d-flex">
                @foreach ( optional($activity->changes)['old'] ? optional($activity->properties)['old'] : [] as $key =>  $item)
                    <div class="column-flex">
                        <div class="d-block pr-2" style="width: 100px;">
                            <strong>{{ $key }}</strong>
                        </div>
                        <div class="d-block pr-2" style="width: 100px;">
                            {{ $item }}
                        </div>
                    </div>
                @endforeach
                <hr>
            </div>
        @endif
        <h4>{{$activity->description == 'deleted' ? 'Deleted Value' : 'New Value'}}</h4>
        <div class="d-flex">
            @foreach ($activity->changes['attributes'] as $key => $item)
                <div class="column-flex">
                    @if($activity->description != 'updated')
                        <div class="d-block pr-2" style="width: 100px;">
                            <strong>{{ $key }}</strong>
                        </div>
                    @endif
                        <div class="d-block pr-2" style="width: 100px;">
                            {{ $item }}
                        </div>
                </div>
            @endforeach
        </div>
    </td>
</tr>
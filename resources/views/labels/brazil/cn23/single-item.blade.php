<tr class="no-border">
    <td>{{ $item->sh_code }}</td>
    <td>{{ $item->quantity }}</td>
    <td colspan="2">
        @if( strlen($item->description) > 34)  
            <span style="font-size: 4.8px"> {{ $item->description }} </span> 
        @else 
            {{ $item->description }}
        @endif
        {{-- extra space --}}
        @if ($loop->first)
            @for($i=strlen($item->description);$i<52;$i=$i+3) 
                &nbsp; &nbsp; &nbsp;
            @endfor
        @endif 
    </td>
    <td>{{ $item->weight }}</td>
    <td>{{ number_format($item->value,2) }}</td>
    <td>{{ number_format($item->value*$item->quantity,2) }}</td>
</tr>
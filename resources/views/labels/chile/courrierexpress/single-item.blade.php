<tr class="no-border">
    <td>{{ $item->sh_code }}</td>
    <td>{{ $item->quantity }}</td>
    <td>{{ $item->description }} </td>
    <td>{{ $item->weight }}</td>
    <td>USD {{ number_format($item->value,2) }}</td>
    <td>USD {{ number_format($item->value*$item->quantity,2) }}</td>
</tr>
<tr>
    <td>
        FRETE (US)
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>
        @if (number_format($order->user_declared_freight,2) == 0.01)
            0.00
        @else
            {{ number_format($order->user_declared_freight,2) }}
        @endif
    </td>
</tr>
<tr>
    <td>
        SEGURO (US)
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>0.00</td>
</tr>
<tr>
    <td>
        TOTAL:
    </td>
    <td>{{ number_format($totalQuantity,2) }}</td>
    <td></td>
    <td>{{ $totalWeight }}Kg</td>
    <td></td>
    <td>{{ number_format($totalValue + ($order->user_declared_freight??0),2) }}</td>
</tr>
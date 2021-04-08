<tr>
    <td>
        FRETE (US)
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>{{ number_format($order->user_declared_freight,2) }}</td>
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
    <td>{{ number_format($totalValue,2) }}</td>
</tr>
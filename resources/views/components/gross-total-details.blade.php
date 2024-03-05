<span>
<i class="fa fa-info"></i>
<div class="tooltip-text">
    <table>
        <tbody>
            <tr>
                <td>Shipping Cost</td>
                <td> {{$order->shipping_value." $"}}</td>
            </tr>
            @if($order->insurance_value)<tr>
                <td> Insurance value </td>
                <td> {{$order->insurance_value." $"}}</td>
            </tr> @endif
            @if($order->dangrous_goods)<tr>
                <td> Dangrous Goods </td>
                <td> {{ $order->dangrous_goods." $" }}</td>
            </tr> @endif
            @if($order->consolidation)<tr>
                <td>Consolidation </td>
                <td>{{$order->consolidation." $"}} </td>
            </tr> @endif
            @if($order->user_profit)<tr>
                <td> User Profit</td>
                <td>{{$order->user_profit." $"}}</td>
            </tr> @endif 
            <tr>
                <td> Additional Service</td>
                <td>{{$order->calculateAdditionalServicesCost($order->services)." $"}} </td>
            </tr> 
            @if($order->tax_and_duty)
            <tr>
                <td> Taxes & Duties</td>
                <td>{{number_format($order->tax_and_duty , 2)." $"}} </td>
            </tr>
            @endif
            @if($order->fee_for_tax_and_duty)<tr>
                <td> Fee Taxes & Duties</td>
                <td>{{number_format($order->fee_for_tax_and_duty , 2)." $"}} </td>
            </tr>@endif
            <tr>
                <td> Total Amount</td>
                <td>{{number_format($order->gross_total , 2)." $"}} </td>
            </tr> 
        </tbody>
    </table>
</div>

</span>

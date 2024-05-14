<span>
    <i class="fa fa-info"></i>
    <div class="tooltip-text">
        <table>
            <tbody>
                @if((setting('is_prc_user', null, $order->user_id)|| strtolower($order->tax_modality)=="ddp" ) &&!($order->shippingService->usps_service_sub_class??false)  )
                <tr>
                    <td>Shipping Cost</td>
                    <td colspan="2"> {{$order->shipping_value." $"}}</td>
                </tr>
                @if($order->insurance_value)
                <tr>
                    <td> Insurance value </td>
                    <td colspan="2"> {{$order->insurance_value." $"}}</td>
                </tr> 
                @endif
                @if($order->dangrous_goods)<tr>
                    <td> Dangrous Goods </td>
                    <td colspan="2"> {{ $order->dangrous_goods." $" }}</td>
                </tr> @endif
                @if($order->consolidation)<tr>
                    <td>Consolidation </td>
                    <td colspan="2">{{$order->consolidation." $"}} </td>
                </tr> @endif
                @if($order->calculated_user_profit)<tr>
                    <td> User Profit</td>
                    <td colspan="2"> {{ $order->calculated_user_profit." $"}}</td>
                </tr>
                @endif
                @foreach($order->services as $service)
                <tr>
                    <td>{{ $service->name}}</td>
                    <td colspan="2">{{$service->price." $"}} </td>
                </tr>
                @endforeach
                @if($order->tax_and_duty)
                <tr>
                    <td> Taxes & Duties</td>
                    <td colspan="2">{{number_format($order->tax_and_duty , 2)." $"}} </td>
                </tr>
                @endif
                @if($order->fee_for_tax_and_duty)<tr>
                    <td> HomePay Convenience Fee</td>
                    <td colspan="2">{{number_format($order->fee_for_tax_and_duty , 2)." $"}} </td>
                </tr>@endif
                <tr>
                    
                    <td> &nbsp; &nbsp; Total Amount &nbsp; &nbsp; &nbsp;</td>
                    <td> &nbsp; &nbsp; {{number_format($order->gross_total , 2)." $"}}  &nbsp; &nbsp; </td>
                    <td></td> 
                </tr>
                @else
                <tr>
                    <td> &nbsp; &nbsp; Freight Value &nbsp; &nbsp; &nbsp;</td>
                    <td> &nbsp; &nbsp; {{number_format($order->user_declared_freight , 2)." $"}} &nbsp; &nbsp; </td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</span>
<div class="items-table">
    <table border="1" style="width: 370px !important">
        <tbody>
            <tr>
                <td colspan="4">
                    CUSTOMS DECLARATION
                </td>
                <td colspan="3">
                    
                </td>
            </tr>
            
            @include('labels.senegal.cn23.items-header')

            @foreach ($items as $item)
            @include('labels.senegal.cn23.single-item')
            @endforeach

            @include('labels.senegal.cn23.items-footer',[
                'totalQuantity' => $order->items->sum('quantity'),
                'totalWeight' => $order->getOriginalWeight('kg'),
                'totalValue' => $order->items()->sum(\DB::raw('quantity * value')),
                'isSumplimentary' => false,
            ])
            
        </tbody>
    </table>
</div>
<div class="items-table">
    <table border="1" style="width: 370px !important">
        <tbody>
            <tr>
                <td colspan="4">
                    CUSTOMS DECLARATION
                </td>
                <td colspan="3">
                    CAN BE OPENED EX OFFICIO 1/2
                </td>
            </tr>
            
            @include('labels.pasarex.cn23.items-header')

            @foreach ($items as $item)
            @include('labels.pasarex.cn23.single-item')
            @endforeach

            @include('labels.pasarex.cn23.items-footer',[
                'totalQuantity' => $order->items->sum('quantity'),
                'totalWeight' => $order->getOriginalWeight('kg'),
                'totalValue' => $order->items()->sum(\DB::raw('quantity * value')),
                'isSumplimentary' => false,
            ])
            
        </tbody>
    </table>
</div>
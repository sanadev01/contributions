<div class="items-table">
    <table border="1">
        <tbody>
            <tr>
                <td colspan="3">
                    DECLARACÃO PARA ALFÃNDEGA
                </td>
                <td colspan="3">
                    PODE SER ABERTO EX OFICIO 1/2
                </td>
            </tr>
            
            @include('labels.brazil.cn23.items-header')

            @foreach ($items as $item)
            @include('labels.brazil.cn23.single-item')
            @endforeach

            @include('labels.brazil.cn23.items-footer',[
                'totalQuantity' => $order->items->sum('quantity'),
                'totalWeight' => $order->getOriginalWeight('kg'),
                'totalValue' => $order->items()->sum(\DB::raw('quantity * value')),
                'isSumplimentary' => false,
            ])
            
        </tbody>
    </table>
</div>
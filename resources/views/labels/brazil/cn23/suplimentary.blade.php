<div class="items-table" style="top: 0.2cm;">
    <table border="1">
        <tbody>
            <tr>
                <td colspan="3">
                    Suplementary:
                </td>
                <td colspan="3">
                    {{ $order->corrios_tracking_code }}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    DECLARACÃO PARA ALFÃNDEGA
                </td>
                <td colspan="3">
                    
                </td>
            </tr>

            @include('labels.brazil.cn23.items-header')

            @foreach ($items as $item)
            @include('labels.brazil.cn23.single-item')
            @endforeach

            @include('labels.brazil.cn23.items-footer',[
                'totalQuantity' => $order->items->sum('quantity'),
                'totalWeight' => $order->getWeight('kg'),
                'totalValue' => $order->items->sum(\DB::raw('quantity * value')),
            ])

        </tbody>
    </table>
</div>
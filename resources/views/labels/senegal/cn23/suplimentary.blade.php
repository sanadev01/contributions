<div class="items-table" style="top: 0.2cm;">
    <table border="1" style="margin-left:5px;width: 370px !important">
        <tbody>
            <tr>
                <td colspan="4">
                    Suplementary:
                </td>
                <td colspan="3">
                    {{ $order->corrios_tracking_code }}
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    DECLARACÃO PARA ALFÃNDEGA
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
                'isSumplimentary' => true,
            ])

        </tbody>
    </table>
</div>
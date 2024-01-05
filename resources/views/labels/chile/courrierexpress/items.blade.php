<div class="items-table">
    <table border="1">
        <tbody>
            <tr>
                <td colspan="4">
                   customs declaration
                </td>
                <td colspan="3">
                    PODE SER ABERTO EX OFICIO 1/2
                </td>
            </tr>
            
            @include('labels.chile.courrierexpress.items-header')

            @foreach ($items as $item)
            @include('labels.chile.courrierexpress.single-item')
            @endforeach

            @include('labels.chile.courrierexpress.items-footer',[
                'totalQuantity' => $order->items->sum('quantity'),
                'totalWeight' => $order->getWeight('kg'),
                'totalValue' => $order->items()->sum(\DB::raw('quantity * value')),
            ])
            
        </tbody>
    </table>
</div>
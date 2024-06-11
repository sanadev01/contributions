<div class="row">
    <div class="table-responsive col-12">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Correios Brazil</th>
                    <th>Correios Chile</th>
                    <th>UPS</th>
                    <th>USPS</th>
                    <th>Fedex</th>
                    <th>Old Services</th>
                </tr>
                <tr>
                    <td>{{ $userOrderCount->brazil_order_count }}</td>
                    <td>{{ $userOrderCount->chile_order_count }}</td>
                    <td>{{ $userOrderCount->ups_order_count }}</td>
                    <td>{{ $userOrderCount->usps_order_count }} </td>
                    <td>{{ $userOrderCount->fedex_order_count }} </td>
                    <td>{{ $userOrderCount->other_order_count }} </td>
                </tr>  
            </tbody>
        </table>
    </div>
</div>
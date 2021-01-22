<section class="card invoice-page">
    <div class="col-12 row">
        <h2>Order Edit</h2>
    </div>
    @if($order)
        <livewire:import-excel.order.edit-parcel :order="$order"/>
        <livewire:import-excel.order.edit-sender :order="$order"/>
        <livewire:import-excel.order.edit-recipient :order="$order"/>
        <livewire:import-excel.order.edit-item :order="$order"/>
    @endif

</section>


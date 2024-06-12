<section class="card invoice-page">
    <div class="col-12 row">
        <h2>@lang('orders.import-excel.Order Edit')</h2>
    </div>
    @if($order)
        <livewire:import-excel.order.edit-parcel :order="$order" :edit="$edit"/>
        <livewire:import-excel.order.edit-sender :order="$order" :edit="$edit"/>
        <livewire:import-excel.order.edit-recipient :order="$order" :edit="$edit"/>
        <livewire:import-excel.order.edit-item :order="$order" :edit="$edit"/>
    @endif
     
</section>


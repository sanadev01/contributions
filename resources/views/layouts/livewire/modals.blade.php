@switch($modalType)
    @case('pre-alert-detail')
        <x-modals.pre-alert-detail :id="$modalDataId"></x-modals.pre-alert-detail>
        @break
    @case('shipment-detail')
        <x-modals.shipment-detail :id="$modalDataId"></x-modals.shipment-detail>
        @break
    @case('order-detail')
        <x-modals.order-detail :id="$modalDataId"></x-modals.order-detail>
        @break
    @case('invoice-detail')
        <x-modals.invoice-detail :id="$modalDataId"></x-modals.invoice-detail>
        @break
    @case('order-tracking')
        <x-modals.order-tracking :id="$modalDataId"></x-modals.order-tracking>
        @break
    @case('profit-detail')
        <x-modals.profit-detail :id="$modalDataId"></x-modals.profit-detail>
        @break
    @case('order-status')
        <x-modals.order-status :id="$modalDataId"></x-modals.order-status>
        @break
    @case('receipt-detail')
        <x-modals.receipt-detail :id="$modalDataId"></x-modals.receipt-detail>
        @break
    @default
        Invalid Modal Type
@endswitch
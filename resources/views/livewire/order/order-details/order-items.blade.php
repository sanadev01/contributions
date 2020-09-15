<div class="my-5">
    <h3 class="my-3 border-bottom-light py-2">Order Items</h3>
    <div class="row my-3">
        <div class="col-12">
            <button class="btn btn-success" type="button" role="button" wire:click="addItem">Add Item</button>
        </div>
    </div>
    @foreach ($items as $key => $item)
        <livewire:order.order-details.order-item :key-id="$key" :item="$item" :key="$key"/>
    @endforeach

    @include('layouts.livewire.loading')
</div>


@push('lvjs-stack')
<script>
    document.addEventListener('DOMContentLoaded', function () {

            $('body').on('change','.items input.quantity,.items input.value',function(){
                let quantity = $(this).closest('.items').find('.quantity').val();
                let unitValue = $(this).closest('.items').find('.value').val();
                $(this).closest('.items').find('.total').val(
                    parseFloat(quantity) * parseFloat(unitValue)
                );
               
               
            });
    })
</script>
@endpush
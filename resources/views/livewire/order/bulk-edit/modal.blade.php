<div class="position-absolute float-right w-50 h-auto bg-light p-3 shadow {{ $shown ? 'd-flex flex-column' : 'd-none' }}" style="top: 60px; right:0" id="id-edit-modal">
    @if ($order)
        <div class="card mb-1">
            <div class="card-content">
                <div class="card-body">
                    <livewire:order.bulk-edit.sender-edit :order="$order" :key="$order->id"/>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <livewire:order.bulk-edit.recipient-edit :recipient="$order->recipient" :key="$order->recipient->id"/>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <livewire:order.bulk-edit.items-edit :items="$order->items" :key="$order->id"/>
                </div>
            </div>
        </div>
    @endif
    <button class="btn btn-danger position-absolute" style="top: 0; right:0px" wire:click="$emit('close-order-edit')">
        &times;
    </button>
    @include('layouts.livewire.loading')
</div>
@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            @this.on('close-order-edit',function(){
                $('#id-edit-modal').removeClass('d-flex');
                $('#id-edit-modal').addClass('d-none');
                // $('#order-table').removeClass('w-25');
                // $('#order-table').addClass('w-100');
                $('.edit-order').prop('checked', false);
            })
            livewire.on('toggle-collapse',function(id){
               $(id).toggleClass('show')
            })
        })
    </script>
@endpush
<div class="position-absolute w-50 bg-light p-3 shadow {{ $shown ? 'd-flex flex-column' : 'd-none' }}" style="top: 60px; right:0" id="id-edit-modal">
    <div class="position-sticky" style="top: 0">
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
</div>
@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            @this.on('close-order-edit',function(){
                $('#id-edit-modal').removeClass('d-flex');
                $('#id-edit-modal').addClass('d-none');
                $('.edit-order').prop('checked', false);
            })
            livewire.on('toggle-collapse',function(id){
               $(id).toggleClass('show')
            })

            $(window).on('scroll',function(){
                if ( $('body').offset().top <0 ){
                    $('#id-edit-modal').css({'top':($('body').offset().top*-5)-100,'position':'fixed !important','z-index':90909090, 'overflow-y':'auto', height:'100vh'});
                }else{
                    $('#id-edit-modal').css({'top':0,'position':'absolute'});
                }
            });
        })
    </script>
@endpush
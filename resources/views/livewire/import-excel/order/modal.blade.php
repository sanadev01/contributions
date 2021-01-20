<div  class="position-absolute w-75 bg-light p-3 shadow {{ $shown ? 'd-flex flex-column' : 'd-none' }}" style="top: 60px; right:0" id="id-edit-modal">
    <div class="position-sticky" style="top: 0">
        <section class="card invoice-page">
            <div class="col-12 row no-print">
                <h2>Edit Order</h2>
            </div>
            @if($order)
                <livewire:import-excel.order.edit-parcel :order="$order"/>
                <livewire:import-excel.order.edit-sender :order="$order"/>
                <livewire:import-excel.order.edit-recipient :order="$order"/>
                <livewire:import-excel.order.edit-item :order="$order"/>
            @endif
        
        </section>
        <button class="btn btn-danger position-absolute" style="top: 0; right:0px" wire:click="$emit('close-order-edit')">
            &times;
        </button>
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


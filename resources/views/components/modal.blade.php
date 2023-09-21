<div class="modal fade" id="hd-modal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h5 class="modal-title">Vertically Centered</h5> --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="h1 text-center"><i class="fa fa-spinner fa-spin"></i></div>
            </div>
        </div>
    </div>
</div>

<script>

    var currentModalRequest= null;
    $('#hd-modal').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget)  
        var modal = $(this)
        button.removeData('modal-type');
        button.removeData('url');
        button.removeData('content');

        var modalType = button.data('modal-type');
        var url = button.data('url');
        
        if ( modalType == 'html' ){
            modal.find('.modal-body').html(
                button.data('content')
            )
            return;
        }
        
        currentModalRequest = $.get(url)
        .done(function(data){
            modal.find('.modal-body').html(
                data
            )
            window.livewire.rescan();
        })
        
        .fail(function(error){
            modal.find('.modal-body').html(
                error
            )
        })
        
    })

    $('#hd-modal').on('hide.bs.modal',function(){
        $('#hd-modal .modal-body').html(`<div class="h1 text-center"><i class="fa fa-spinner fa-spin"></i></div>`);
        console.log("canceld")
        if ( currentModalRequest ){
            currentModalRequest.abort();
        }
    });

</script>
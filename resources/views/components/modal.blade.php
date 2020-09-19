<div class="modal fade" id="hd-modal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h5 class="modal-title">Vertically Centered</h5> --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="h1">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $('#hd-modal').on('show.bs.modal', function (event) {
        console.log('clicked')
        var button = $(event.relatedTarget)  
        var modal = $(this)
        var modalType = button.data('modal-type');
        var url = button.data('url');
        
        if ( modalType == 'html' ){
            modal.find('.modal-body').html(
                button.data('content')
            )
            return;
        }

        $.get(url)
            .done(function(data){
                modal.find('.modal-body').html(
                    data
                )
            })
            .fail(function(error){
                modal.find('.modal-body').html(
                    error
                )
            })

    })

</script>
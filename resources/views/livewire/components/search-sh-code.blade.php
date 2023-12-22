<div>
    <select required class="form-control" name="{{$name}}" wire:model.debounce.500ms="search" id="sh_code" @if($orderInventory) disabled @endif>
        <option value="">Select HS code / Selecione o código HS</option>
        @foreach ($codes as $code)
        <option value="{{ $code->code }}">
            @if(app()->getLocale() == 'en'){{ optional(explode('-------',$code->description))[0] }}@endif
            @if(app()->getLocale() == 'pt'){{ optional(explode('-------',$code->description))[1] }}@endif
            @if(app()->getLocale() == 'es'){{ optional(explode('-------',$code->description))[2] }}@endif
        </option>
        @endforeach
    </select>

    <!-- Modal -->
    <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="exampleModalLabel">Warning Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-justify">
                        @lang('orders.order-details.Warning Message')
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('#sh_code').selectpicker({
                    liveSearch: true,
                    liveSearchPlaceholder: 'Search...',
                });

            }, 4000);

        });

        window.addEventListener('checkShCode', event => {

            let code = event.detail.sh_code;

            if (code == 490199) {
                $('#warningModal').modal('show');
            }
        })
    </script>

</div>
<div class="items shadow p-4 border-top-success border-2 mt-2">
    @if (session()->has('success'))
        <div class="alert alert-success" wire-ignore>
            {{ session('success') }}
        </div>
        @endif 
    
    <div class="row mt-1">

        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Harmonized Code')<span class="text-danger"></span></label>
                <input type="hidden" wire:model="editItemId">
                {{$type}}
                <select class="form-control sh_code" wire:model="sh_code" onclick="initializeSelectpicker()">
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
                
                @error("sh_code")
                <div class="help-block text-danger">{{ $message }}</div>
                @enderror
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
            </div>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Description') <span id="feedback" wire:ignore></span></label>
                <input type="text" wire:model="description" class="form-control descp" id="description" max="500" min="0" onkeyup="descriptionChange()">
                <small id="characterCount" class="form-text text-muted" wire:ignore></small>

                @error("description")
                <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Quantity') <span class="text-danger"></span></label>
                <input type="number" class="form-control quantity" wire:model="quantity" step="0.01" onkeydown="if(event.key==='.'){event.preventDefault();}" oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');" min="1"  @if($order->products->isNotEmpty()) readonly @endif>
                @error("quantity")
                <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Unit Value') <span class="text-danger"></span></label>
                <input type="number" class="form-control value" wire:model="value" step="0.01" min="0.01"  >
                @error("value")
                <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Total') <span class="text-danger"></span></label>
                <input type="number" readonly class="form-control total" value="{{ $totalValue }}">
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls"> 
                <label class="d-flex">@lang('orders.order-details.order-item.Is Contains Dangrous Goods') </label>
                <select wire:model="dangrous_item" class="form-control dangrous">
                    <option value="0">No</option>
                    <option value="contains_battery" {{ $contains_battery == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Battery')</option>
                    <option value="contains_perfume" {{ $contains_perfume == 1 && $contains_battery == 0 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Perfume')</option>
                </select>
                @error("dangrous_item")
                <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-4 text-right">
            <!-- wire:click="$emit('removeItem',{{$keyId}})" -->
            @if($editItemId)
            <button wire:click="submitForm" class="btn btn-success" type="submit">@lang('orders.actions.update-item')</button>
            @else
            <button wire:click="submitForm" class="btn btn-primary" type="submit">@lang('orders.actions.add-item')</button>
            @endif
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
    function descriptionChange() {
        descriptionLength = ($('#description').val()).length;
        $('#feedback').removeClass('text-success  text-danger');
        serviceCode = Number($('#shipping_service_id option:selected').attr('data-service-code'));

        var correios = <?php echo json_encode($correios); ?>;
        var isCorreios = correios.indexOf(serviceCode) !== -1;
        var geps = <?php echo json_encode($geps); ?>;
        var isGeps = geps.indexOf(serviceCode) !== -1;

        var prime5 = <?php echo json_encode($prime5); ?>;
        var isPrime5 = prime5.indexOf(serviceCode) !== -1;

        if (isPrime5) {
            limit = 60;
        } else if (isGeps) {
            limit = 50;
        } else if (isCorreios) {
            limit = 500;
        } else {
            limit = 200;
        }
        if (descriptionLength > limit) {
            $('#description').val($('#description').val().substr(0, limit));
            descriptionLength = limit;
        }

        $('#characterCount').text(' ' + descriptionLength + '/' + limit);
        if (descriptionLength <= 50 && descriptionLength < limit) {

            if (limit <= 60 && descriptionLength > (limit / 2))
                updateFeedback('Good Description!', true)
            else
                updateFeedback('Weak Description!', false)
        } else if (descriptionLength <= 150 && descriptionLength < limit)
            updateFeedback('Good Description!', true)
        else if (descriptionLength <= 500 && descriptionLength < limit)
            updateFeedback('Very Good Description!', true)
        else
            updateFeedback('Limit Exceeded!', false)
    }

    function updateFeedback(message, isValidFeedback, id) {
        $('#feedback').addClass(isValidFeedback ? 'text-success' : 'text-danger');
        $('#feedback').text(message);
    }
</script>

<script>
    window.addEventListener('checkShCode', event => {
        let code = event.detail.sh_code;
        if (code == 490199) {
            $('#warningModal').modal('show');
        }
    })
    $('.sh_code').on('change', function() {
        initializeSelectpicker();
    })
    function initializeSelectpicker() {
        $('#loading').fadeIn();
        $('.sh_code').selectpicker('destroy');
        setTimeout(() => {
            $('.sh_code').selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: 'Search...',
            });
            $('#loading').fadeOut();
        }, 2500);
    }
</script>
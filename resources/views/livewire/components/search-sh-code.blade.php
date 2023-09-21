{{-- <div class="position-relative">
    <input type="search" autocomplete="off" required class="form-control" name="{{$name}}" wire:model.debounce.500ms="search">
    @error( $name )
        <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 10000;max-height:200px;overflow-y:auto;">
        @if (!empty($codesList))
            <div class="w-100 shadow p-2">
                @foreach ($codesList as $shCode)
                    <div class="w-100 border-bottom-light p-2 cursor-pointer" wire:click="selectCode('{{$shCode['code']}}')">
                        {{ $shCode['code'] }} | {{ $shCode['description'] }}
                    </div>
                @endforeach
            </div>
        @elseif( strlen($search) && !$valid)
        <div class="w-100 shadow-lg text-center">
            No Results
        </div>
        @endif
    </div>
</div> --}}
<div>

<select required class="form-control" name="{{$name}}" wire:model.debounce.500ms="search" id="sh_code" @if($orderInventory) disabled @endif>
    <option value="">Select HS code / Selecione o código HS</option>
    @foreach ($codes as $code)
        <option value="{{ $code->code }}">{{ $code->description }}</option>
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

<script>
    window.addEventListener('checkShCode', event => {
        
        let code = event.detail.sh_code;
        
        if(code == 490199) 
        {
            $('#warningModal').modal('show');
        }
    })
</script>

</div>

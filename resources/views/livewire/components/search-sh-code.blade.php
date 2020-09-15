<div class="position-relative">
    <input type="search" autocomplete="off" class="form-control" name="sh_code" wire:model.debounce.500ms="search">
    @error('sh_code')
        <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 100">
        @if (!empty($codesList))
            <div class="d-flex w-100 shadow-lg">
                @foreach ($codesList as $shCode)
                    <div class="w-100 border-bottom-light p-2 cursor-pointer" wire:click="selectCode('{{$shCode['code']}}')">
                        {{ $shCode['code'] }} | {{ $shCode['description'] }}
                    </div>
                @endforeach
            </div>
        @elseif( strlen($search))
        <div class="w-100 shadow-lg text-center">
            No Results
        </div>
        @endif
    </div>
</div>

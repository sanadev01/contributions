<div class="position-relative">
    <input type="search" autocomplete="off" class="form-control" name="origin_operator_name" wire:model.debounce.500ms="search" placeholder="Search Airport by IATA Code">

    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 100">
        @if (!empty($airport) && strlen($search) > 2)
            <div class="d-flex w-100 shadow-lg flex-column">
                    <div class="w-100 border-bottom-light p-2 cursor-pointer" wire:click="selectAirport('{{$airport['iata']}}')">
                        <strong>Name:</strong> {{ $airport['name']}} <br>
                        <strong>Location:</strong> {{ $airport['location']}} <br>
                        <strong>Iata Code:</strong> {{ $airport['iata']}} <br>
                    </div>
            </div>
        @endif
    </div>
    @if ($message && $textClass && strlen($search) > 2)
        <div class="mt-3">
            <span><p class="{{$textClass}}">{{$message}}</p></span>
        </div>
    @endif
</div>

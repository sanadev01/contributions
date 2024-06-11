<div class="position-relative">
    <input type="search" autocomplete="off" class="form-control" name="user" wire:model.debounce.500ms="search">
    <input type="hidden" name="user_id" value="{{$userId}}">
    @error('user_id')
        <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 100">
        @if (!empty($usersList))
            <div class="d-flex w-100 shadow-lg flex-column">
                @foreach ($usersList as $user)
                    <div class="w-100 border-bottom-light p-2 cursor-pointer" wire:click="selectUser('{{$user['id']}}','{{$user['name']}}')">
                        <strong>Name:</strong> {{ $user['name'] }} <br>
                        <strong>Email:</strong> {{ $user['email'] }} <br>
                        <strong>Pobox:</strong> {{ $user['pobox_number'] }} <br>
                    </div>
                @endforeach
            </div>
        @elseif( strlen($search) && !$userId)
        <div class="w-100 shadow-lg text-center">
            No Results
        </div>
        @endif
    </div>
</div>

<div>
    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">@lang('user.Referrer')<span class="text-danger"></span></label>
        <div class="col-md-6">
            <select name="referrer_id" class="form-control"  wire:model="referrer_id">
                <option value="" selected disabled hidden>@lang('user.Select Referrer')</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <div class="help-block"></div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>

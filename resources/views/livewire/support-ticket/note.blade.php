    <div class="form-group col-3">
        <div class="controls">
            <label class="label-control">@lang('tickets.Notes')</label>
            <textarea class="form-control" rows="10"  wire:model.defer="note"></textarea>
            @error('note')
                <div class="help-block text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="" class="btn btn-primary pull-right mt-2" wire:click.prevent="save">
            @lang('tickets.Save Note')
        </button>
        @include('layouts.livewire.loading')
    </div>

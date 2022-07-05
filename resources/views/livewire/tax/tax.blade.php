<div>
    <div class="row m-1">
        <div class="form-group col-sm-6 col-md-3">
            <div class="controls">
                <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                <livewire:components.search-user />
                @error('pobox_number')
                <div class="help-block text-danger"> {{ $message }} </div>
                @enderror
            </div>
        </div>
        <div class="form-group col-sm-6 col-md-3">
            <div class="controls">
                <label>Tracking No.<span class="text-danger">*</span></label>
                <textarea type="text" placeholder="Please Enter Tracking Codes" rows="2" class="form-control" wire:model.debounce.500ms="trackings"></textarea>
                @error('pobox_number')
                    <div class="help-block text-danger"> {{ $message }} </div>
                @enderror
            </div>
        </div>
        <div class="form-group col-sm-6 col-md-3">
            <button type="" class="btn btn-primary mt-5" wire:click.prevent="sreach">
                Find
            </button>
        </div>
    </div>
</div>

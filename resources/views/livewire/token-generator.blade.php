<div>
    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">Application Token<span class="text-danger">*</span></label>
        <div class="col-md-5">
            <textarea type="text" rows="4" class="form-control" name="token" placeholder="Token">{{  $user->api_token  }}</textarea>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" type="button" wire:click="revoke">Revoke</button>
        </div>
    </div>

    <div class="position-absolute">
        @include('layouts.livewire.loading')
    </div>
</div>

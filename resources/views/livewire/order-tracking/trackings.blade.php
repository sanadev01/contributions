<div>
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <input type="text" placeholder="Enter Tracking Number or order ID" class="form-control col-8 w-100 text-center border border-primary" style="height: 50px; font-size: 30px;" wire:model.debounce.500ms="trackingNumber">
        </div>
        <div class="col-md-4 col-sm-4">
            <button class="btn btn-primary" wire:click="trackOrder">Track</button>
        </div>
    </div>
</div>

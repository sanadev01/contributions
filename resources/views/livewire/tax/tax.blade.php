<div>
    <form wire:submit.prevent="submit">
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
                    <textarea type="text" placeholder="Please Enter Tracking Codes" rows="2" class="form-control" wire:model.defer="trackingNumbers"></textarea>
                    @error('trackingNumbers')
                        <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-sm-6 col-md-3">
                <button type="submit" class="btn btn-primary mt-5" wire:click="search">Find</button>
            </div>
        </div>
    </form></br>
    <form class="form" action="{{ route('admin.inventory.product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
            @if(!is_null($orders))
                <div class="row m-1 mb-2">
                    <div class="col-md-2">
                        <label><b>@lang('tax.Order ID')</b></label>
                    </div>
                    <div class="col-md-2">
                        <label><b>@lang('tax.User Name')</b></label>
                    </div><div class="col-md-2">
                        <label><b>@lang('tax.Tracking Code')</b></label>
                    </div><div class="col-md-3">
                        <label><b>@lang('tax.Tax Payment 1')</b></label>
                    </div><div class="col-md-3">
                        <label><b>@lang('tax.Tax Payment 2')</b></label>
                    </div>
                </div>
                @foreach($orders as $order)
                    <div class="row m-1 mb-3">
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="order_id" value="{{ $order->warehouse_number }}" readonly required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="user_name" value="{{ $order->user->name }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="tracking_code" value="{{ $order->corrios_tracking_code }}" readonly required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="tax_1" value="" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="tax_2" value="" required>
                        </div>
                    </div>
                @endforeach
            @endif

        <div class="row mt-4 mb-4">
            <div class="col-12 d-flex text-center flex-sm-row flex-column justify-content-end mt-1">
                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-4 waves-effect waves-light">
                    @lang('tax.Pay')
                </button>
            </div>
        </div>
    </form>
</div>

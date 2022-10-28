<div>
    <div class="border p-2 position-relative">
        <h3 class="bg-white shadow-sm p-2" data-toggle="collapse" data-target="#senderCollapse">@lang('orders.order-details.Sender')</h3>
        <fieldset id="senderCollapse" class="collapse show" aria-expanded="false" role="tabpanel"
            aria-labelledby="steps-uid-0-h-0" aria-hidden="false">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="firstName1">@lang('orders.sender.First Name') </label>
                        <input type="text" class="form-control" name="first_name" required
                            wire:model.defer="sender_first_name" id="firstName1">
                        @error('sender_first_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="lastName1">@lang('orders.sender.Last Name')</label>
                        <input type="text" class="form-control" name="last_name" wire:model.defer="sender_last_name">
                        @error('sender_last_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Email')</label>
                        <input type="email" class="form-control" name="email" wire:model.defer="sender_email">
                        @error('sender_email')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Phone')</label>
                        <input type="text" class="form-control" name="phone" wire:model.defer="sender_phone">
                        @error('sender_phone')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Tax Id')</label>
                        <input type="text" class="form-control" name="taxt_id" wire:model.defer="sender_taxId">
                        @error('sender_taxId')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row col-12 text-right">
                    <div class="col-11 text-right">
                        @if (!$edit)
                            @if (!$order->error)
                                <div class="text-right">
                                    <a href="{{ route('admin.import.import-excel.show', $order->import_id) }}"
                                        class="btn btn-success">
                                        Error Fixed
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="col-1 text-right">
                        <button class="btn btn-primary" wire:click="save">
                            @lang('orders.create.save')
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100"
                style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
</div>

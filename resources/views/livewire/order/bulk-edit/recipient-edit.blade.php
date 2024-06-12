<div class="border p-2 position-relative">
    <h2 class="bg-white shadow-sm p-2" data-toggle="collapse" data-target="#recipientCollapse">@lang('orders.order-details.Recipient')</h2>
    <fieldset  id="recipientCollapse" class="collapse show" aria-expanded="false" role="tabpanel" aria-labelledby="steps-uid-0-h-0" aria-hidden="false">
        <div class="row mt-1">
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.First Name') <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" wire:model.defer="first_name"  placeholder="@lang('address.First Name')">
                    @error('first_name')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>

            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Last Name') <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" wire:model.defer="last_name" placeholder="@lang('address.Last Name')">
                    @error('last_name')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>

            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Email') <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="email" wire:model.defer="email" required placeholder="@lang('address.Email')">
                    @error('email')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Phone')</label>
                    <input type="text" class="form-control" name="phone" wire:model.defer="phone" required placeholder="+55123456789">
                    @error('phone')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Address') <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="address" wire:model.defer="address" required placeholder="@lang('address.Address')"/>
                    @error('address')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Address')2</label>
                    <input type="text" class="form-control"  placeholder="@lang('address.Address')2" wire:model.defer="address2"  name="address2">
                    @error('address2')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Street No')</label>
                    <input type="text" class="form-control" placeholder="@lang('address.Street No')" wire:model.defer="street_no"  name="street_no">
                    @error('street_no')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <div class="controls">
                        <label>@lang('address.Country') <span class="text-danger">*</span></label>
                        <select id="country"  name="country_id" class="form-control selectpicker show-tick" wire:model.defer="country_id" data-live-search="true">
                            <option value="">Select @lang('address.Country')</option>
                            @foreach (countries() as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')
                        <div class="help-block text-danger"> {{ $message }} </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.State') <span class="text-danger">*</span></label>
                    <select name="state_id" id="state" class="form-control selectpicker show-tick" wire:model.defer="state_id" data-live-search="true">
                        <option value="">Select @lang('address.State')</option>
                        @foreach (states($recipient->country_id) as $state)
                            <option value="{{ $state->id }}"> {{ $state->code }} </option>
                        @endforeach
                    </select>
                    @error('state_id')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.City') <span class="text-danger">*</span></label>
                    <input type="text" name="city" wire:model.defer="city" class="form-control"  required placeholder="City"/>
                    @error('city')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>

            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                    <label>@lang('address.Zip Code')</label>
                    <input type="text" name="zipcode" wire:model.defer="zipcode" required class="form-control" placeholder="Zip Code"/>
                    @error('zipcode')
                    <div class="help-block text-danger"> {{ $message }} </div>
                    @enderror
                </div>
            </div>

            <div class="form-group col-12 col-sm-6 col-md-4">
                <div class="controls">
                        <label id="cnpj_label_id" style="{{ optional($recipient)->account_type != 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CNPJ') <span class="text-danger">* (Brazil Only)</span> </label>
                        <label id="cpf_label_id" style="{{ optional($recipient)->account_type == 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CPF') <span class="text-danger">* (Brazil Only)</span> </label>
                        <input type="text" name="tax_id" id="tax_id" wire:model.defer="tax_id" required class="form-control" placeholder="CNPJ"/>
                    @error('tax_id')
                    <div class="help-block"> {{ $message }} </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-right">
                <button class="btn btn-primary" wire:click="save">
                    @lang('orders.create.save')
                </button>
            </div>
        </div>
    </fieldset>
    <div wire:loading>
        <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </div>
</div>

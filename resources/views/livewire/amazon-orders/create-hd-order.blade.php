<div>
    <div class="border p-2 position-relative">
        <h3 class="btn-primary shadow-sm p-2" data-toggle="collapse" data-target="#parcelCollapse"> @lang('orders.import-excel.Parcel')</h3>
        <fieldset  id="parcelCollapse" class="collapse show" aria-expanded="false" role="tabpanel" aria-labelledby="steps-uid-0-h-0" aria-hidden="false">
            @admin
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                        <livewire:components.search-user />
                        @error('pobox_number')
                        <div class="help-block text-danger"> {{ $message }} </div>
                        @enderror
                    </div>
                </div>
            </div>
            @endadmin
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Merchant')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="merchant" placeholder="" wire:model.defer.500ms="merchant">
                        @error('merchant')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Carrier') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model.defer.500ms="carrier">
                        @error('carrier')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Tracking ID')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tracking_id" placeholder="" wire:model.defer.500ms="tracking_id">
                        @error('tracking_id')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mt-1">
                @can('addShipmentDetails', App\Models\Order::class)
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.External Customer Reference')<span class="text-danger"></span></label>
                        <input type="text" class="form-control" placeholder=""  name="customer_reference" wire:model.defer.500ms="customer_reference">
                        @error('customer_reference')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endcan
                <div class="col-12 col-sm-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="controls">
                                    @admin
                                    <label>@lang('parcel.Arrival Date')<span class="text-danger">*</span></label>
                                    @endadmin
                                    @user
                                    <label>@lang('parcel.Order Date')<span class="text-danger">*</span></label>
                                    @enduser
                                    <input type="text" name="order_date" class="form-control order_date_picker datepicker" required="" wire:model.defer.500ms="order_date" placeholder="@user @lang('parcel.Order Date') @enduser @admin @lang('parcel.Arrival Date') @endadmin"/>
                                    @error('order_date')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.correios tracking code')<span class="text-danger"></span></label>
                        <input type="text" class="form-control" placeholder=""  name="correios_tracking_code" wire:model.defer.500ms="correios_tracking_code">
                        @error('customer_reference')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}
                
            </div>

            @can('addWarehouseNumber', App\Models\Order::class)
                <div class="row">
                    <div class="form-group col-12">
                        <div class="controls">
                            <label>@lang('parcel.Warehouse Number') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required name="whr_number" wire:model.defer.500ms="whr_number" placeholder="">
                            @error('whr_number')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endcan

            
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6">
                    <div class="controls">
                        <label>@lang('parcel.Weight') ({{  $unit == 'kg/cm'? 'kg': 'lbs' }})  <span class="text-danger">*</span></label>
                        <input step="0.001" type="number" class="form-control" autocomplete="off" required name="weight" wire:model.debounce.500ms="weight" placeholder=""/>
                        <div class="help-block">
                            <span>{{ $weightOther }}</span>
                            @if ( $unit != 'kg/cm' )
                                <span>kg</span>
                            @else
                                <span>lbs</span>
                            @endif
        
                            @error('weight')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6">
                    <div class="controls">
                        <label>@lang('parcel.Measuring Units') <span class="text-danger">*</span></label>
                        <select name="unit" class="form-control" required wire:model="unit">
                            <option value="">Select</option>
                            <option value="kg/cm">kg/cm</option>
                            <option value="lbs/in">lbs/in</option>
                        </select>
                        @error('unit')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        
            <div class="row mt-1">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Length') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }})<span class="text-danger">*</span></label>
                        <input step="0.001" type="number" class="form-control" autocomplete="off" required name="length" wire:model.debounce.500ms="length" placeholder=""/>
                        <div class="help-block">
                            <span>{{ $lengthOther }}</span>
                            @if ( $unit != 'kg/cm' )
                                <span>cm</span>
                            @else
                                <span>in</span>
                            @endif
                            @error('length')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Width') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger">*</span></label>
                        <input step="0.001" type="number" class="form-control" autocomplete="off" required name="width" wire:model.debounce.500ms="width" placeholder=""/>
                        <div class="help-block">
                            <span>{{ $widthOther }}</span>
                            @if ( $unit != 'kg/cm' )
                                <span>cm</span>
                            @else
                                <span>in</span>
                            @endif
                            @error('width')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.Height') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger">*</span></label>
                        <input step="0.001" type="number" class="form-control" autocomplete="off" required name="height" wire:model.debounce.500ms="height" placeholder=""/>
                        <div class="help-block">
                            <span>{{ $heightOther }}</span>
                            @if ( $unit != 'kg/cm' )
                                <span>cm</span>
                            @else
                                <span>in</span>
                            @endif
                            @error('height')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="controls h2 mt-2">
                        <label> <span class="text-danger">*</span> @lang('parcel.The Rate will be applied on')  <strong class="text-danger h2">{{ $volumeWeight }} <span class="ml-1"> {{ $currentWeightUnit }} </span> </strong></label>
                    </div>
                </div>
            </div>

        </fieldset>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="border p-2 position-relative">
        <h3 class="btn-primary shadow-sm p-2" data-toggle="collapse" data-target="#senderCollapse">@lang('orders.order-details.Sender')</h3>
        <fieldset  id="senderCollapse" class="collapse show" aria-expanded="false" role="tabpanel" aria-labelledby="steps-uid-0-h-0" aria-hidden="false">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                    <label for="firstName1">@lang('orders.sender.First Name') </label>
                        <input type="text" class="form-control" name="first_name" required wire:model.defer="sender_first_name" id="firstName1">
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
        </fieldset>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="border p-2 position-relative mt-2">
        <h3 class="btn-primary shadow-sm p-2" data-toggle="collapse" data-target="#recipientCollapse">@lang('orders.order-details.Recipient')</h3>
        <fieldset  id="recipientCollapse" class="collapse show" aria-expanded="false" role="tabpanel" aria-labelledby="steps-uid-0-h-0" aria-hidden="false">
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        {{ $first_name }}
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
                        <input type="text" class="form-control" min="13" max="15" name="phone" wire:model.defer="phone" required placeholder="+55123456789123">
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
                            <select id="country" name="country_id" class="form-control selectpicker show-tick"
                                    wire:model="country_id" data-live-search="true">
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
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->code }}</option>
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
                            {{-- <label id="cnpj_label_id" style="{{ optional($recipient)->account_type == 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CNPJ') <span class="text-danger">* (Brazil Only)</span> </label>
                            <label id="cpf_label_id" style="{{ optional($recipient)->account_type != 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CPF') <span class="text-danger">* (Brazil Only)</span> </label> --}}
                            <label id="cpf_label_id">@lang('address.CPF') <span class="text-danger">* (Brazil Only)</span> </label>
                            <input type="text" name="tax_id" id="tax_id" wire:model.defer="tax_id" required class="form-control" placeholder="000.000.000-00"/>
                        @error('tax_id')
                        <div class="help-block text-danger"> {{ $message }} </div>
                        @enderror
                    </div>
                </div>
            </div>
        </fieldset>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="border p-2 position-relative">
        <h3 class="btn-primary shadow-sm p-2" data-toggle="collapse" data-target="#shippingCollapse">@lang('orders.import-excel.Shipping & Items')</h3>
        <div id="shippingCollapse" class="collapse show">
            <fieldset role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current p-4" aria-hidden="false">
                <div class="row">
                    <div class="form-group col-12 col-sm-6 col-md-6">
                        <div class="controls">
                            <label>@lang('orders.order-details.Customer Reference') <span class="text-danger"></span></label>
                            <input name="customer_reference" class="form-control"  placeholder="@lang('orders.order-details.Customer Reference')" wire:model.defer="customer_reference"/>
                            @error("customer_reference")
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-md-6">
                        <div class="controls">
                            <label>Shipping Service<span class="text-danger">*</span></label>
                            <select name="shipping_service" id="shipping_service" class="form-control selectpicker show-tick" wire:model="shipping_service" data-live-search="true">
                                <option value="">Select Shipping Service</option>
                                @foreach($shippingServices as $id => $service)
                                    <option value="{{ $id }}">{{ $service }}</option>
                                @endforeach
                            </select>
                            @error('shipping_service')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group col-12 col-sm-6 col-md-6">
                        <div class="controls">
                            <label class="h4">Freight <span class="text-danger"></span></label>
                            <input class="form-control" name="user_declared_freight" placeholder="Freight" wire:model="user_declared_freight"/>
                            @error("user_declared_freight")
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>                    
                    
                </div>
            </fieldset>
        </div>

        <h3 class="bg-white shadow-sm p-2" data-toggle="collapse" data-target="#itemsCollapse">@lang('orders.order-details.Order Items')</h3>
        <div id="itemsCollapse" class="collapse show">
            @foreach ($amazonOrder as $keyId => $item)
                <div class="items shadow p-4 border-top-success border-2 mt-2" wire:key="$item->id">
                    <div class="row mt-1">
                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Harmonized Code')<span class="text-danger"></span></label>
                                <select required class="form-control" name="items[{{$keyId}}][sh_code]" wire:model.defer="items.{{$keyId}}.sh_code">
                                    <option value="">Select HS code / Selecione o código HS</option>
                                    @foreach ($shCodes as $code)
                                        <option value="{{ $code['code'] }}">{{ $code['description'] }}</option>
                                    @endforeach
                                </select>
                                @error("items.{$keyId}.sh_code")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Description') <span class="text-danger"></span></label>
                                <input type="text" class="form-control" required name="items[{{$keyId}}][description]" wire:model.defer="items.{{$keyId}}.description">
                                @error("items.{$keyId}.description")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="form-group col-12 col-sm-4 col-md-4">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Quantity') <span class="text-danger"></span></label>
                                <input type="number" class="form-control quantity" step="0.01" min="1" required name="items[{{$keyId}}][quantity]" wire:model.defer="items.{{$keyId}}.quantity">
                                @error("items.{$keyId}.quantity")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-4 col-md-4">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Unit Value') <span class="text-danger"></span></label>
                                <input type="number" class="form-control value" step="0.01" min="0.01" required name="items[{{$keyId}}][value]" wire:model.defer="items.{{$keyId}}.value">
                                @error("items.{$keyId}.value")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-4 col-md-4">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Total') <span class="text-danger"></span></label>
                                <input type="number" readonly class="form-control total" wire:model.defer="items.{{$keyId}}.value">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="form-group col-12 col-sm-4 col-md-4">
                            <div class="controls">
                                <label class="d-flex">@lang('orders.order-details.order-item.Contains Battery') </label>
                                <select name="items[{{$keyId}}][dangrous_item]" wire:model.defer="items.{{$keyId}}.contains_battery" class="form-control" id="">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                @error("items.{$keyId}.dangrous_item")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-8 col-md-8">
                            <div class="controls">
                                <label class="d-flex">@lang('orders.order-details.order-item.Contains Perfume')  </label>
                                <select name="items[{{$keyId}}][dangrous_item]" wire:model.defer="items.{{$keyId}}.contains_perfume" class="form-control" id="">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                @error("items.{$keyId}.dangrous_item")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <br>
            <div class="row">
                <div class="row col-12 text-right">
                    <div class="col-9 text-right"></div>
                    <div class="col-3 text-right">
                        <button class="btn btn-primary" wire:click="save">
                            Create Order
                        </button>
                    </div>
                </div>
            </div>
            <div wire:loading>
                <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>

    </div>
</div>

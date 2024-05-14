<div class="card-bg rounded card-body font-sans">

    <form action="{{route('us-calculator.store')}}" method="POST">
        <div class="" wire:ignore>
            @csrf
            <div class="" wire:ignore>
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="vs-checkbox-con vs-checkbox-primary" title="from_herco">
                                <input type="checkbox" name="from_herco" id="from_herco"  >
                                <span class="vs-checkbox vs-checkbox-lg">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                            <span class="mt-2">
                                <label class="h3 standard-font color-gray">Domestic</label>
                            </span>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="input-group">
                            <div class="vs-checkbox-con vs-checkbox-primary" title="to_herco">
                                <input type="checkbox" name="to_herco" id="to_herco"  >
                                <span class="vs-checkbox vs-checkbox-lg">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                            <span class="mt-2">
                                <label class="h3 standard-font color-gray" for="to_herco">Domestic</label>
                            </span>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="vs-checkbox-con vs-checkbox-primary" title="to_international">
                                <input type="checkbox" name="to_international" id="to_international">
                                <span class="vs-checkbox vs-checkbox-lg">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                            <span class="mt-2">
                                <label class="h3 standard-font color-gray">International</label>
                            </span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="user_id" id="user_id" value="{{ ((auth()->check()) ? auth()->user()->id : null) }}">
                <div class="row mb-1 mt-3 d-none" id="origin">
                    <div class="controls col-6">
                        <h4 class="color-gray standard-font">Origin: Homedeliverybr MIA</h4>
                    </div>
                </div>
                <div class="d-none" id="sender_info">
                    <div class="row mb-1 mt-3 d-none">
                        <div class="controls col-6">
                            <h4 class="color-gray standard-font bold">Sender Address</h4>
                        </div>
                    </div>
                    <div class="row mb-1 d-none">
                        <div class="controls col-4">
                            <label>Origin Country</label>
                            <select id="origin_country" name="origin_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.Country')</option>
                                <option value="250" selected>United States</option>
                            </select>
                        </div>
                        <div class="controls col-4">
                            <label>Sender State</label>
                            <option value="" selected disabled hidden>Select State</option>
                            <select name="sender_state" id="sender_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.State')</option>
                                @foreach (us_states() as $state)
                            <option {{ 'FL' == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="controls col-4">
                            <label>Sender City</label>
                            <input type="text" id="sender_city" name="sender_city" value="Miami" class="form-control" required placeholder="Sender City" />
                        </div>
                    </div>
                    <div class="row mb-1 d-none">
                        <div class="controls col-4">
                            <label>Sender Address</label>
                            <input type="text" class="form-control" id="sender_address" name="sender_address" value="2200 NW 129TH AVE Suite# 100" required placeholder="Sender Address" />
                        </div>
                        <div class="controls col-4">
                            <label>Sender ZipCode</label>
                            <input type="text" name="sender_zipcode" id="sender_zipcode" value="33182" required class="form-control" placeholder="Zip Code" />
                            <div id="sender_zipcode_response">

                            </div>
                        </div> 
                    </div>
                </div>
                <div class="d-none" id="recipient_info">
                  
                    <div class="row mb-1">
                        <div class="controls col-4" id="all_destination_countries">
                            <label>Destination Country</label>
                            <select id="destination_country" name="destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.Country')</option>
                                @foreach (countries() as $country)
                                <option {{ old('destination_country') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="controls col-4" id="us_destination_country">
                            <label>Destination Country</label>
                            <select id="us_destination_country" name="us_destination_country" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.Country')</option>
                                <option value="250" selected>United States</option>
                            </select>
                        </div>
                        <div class="controls col-4" id="all_destination_states">
                            <label>Recipient State</label>
                            <option value="" selected disabled hidden>Select State</option>
                            <select name="recipient_state" id="recipient_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.State')</option>
                                @foreach (states() as $state)
                                <option {{ old('recipient_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="controls col-4" id="us_destination_states">
                            <label>Recipient State</label>
                            <option value="" selected disabled hidden>Select State</option>
                            <select name="us_recipient_state" id="us_recipient_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                                <option value="">Select @lang('address.State')</option>
                                @foreach (us_states() as $state)
                                <option {{ old('sender_state') == $state->code ? 'selected' : '' }} value="{{ $state->code }}">{{ $state->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="controls col-4">
                            <label>Recipient City</label>
                            <input type="text" id="recipient_city" name="recipient_city" value="{{old('recipient_city')}}" class="form-control" required placeholder="Recipient City" />
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="controls col-4">
                            <label>Recipient Address</label>
                            <input type="text" class="form-control" id="recipient_address" name="recipient_address" value="{{old('recipient_address')}}" required placeholder="Recipient Address" />
                        </div>
                        <div class="controls col-4">
                            <label>Recipient ZipCode</label>
                            <input type="text" name="recipient_zipcode" id="recipient_zipcode" value="{{ cleanString(old('recipient_zipcode')) }}" required class="form-control" placeholder="Zip Code" />
                            <div id="recipient_zipcode_response"></div>
                        </div>
                        <div class="controls col-4 d-none" id="recipient_personal_info">
                                <label>Recipient Phone</label>
                                @livewire('components.search-address', ['user_id' => ((auth()->check()) ? auth()->user()->id : null), 'from_calculator' => true ])
                         </div>
                    </div>
                </div>
                <div class="row mb-1 mt-3 d-none" id="destination">
                    <div class="controls col-6">
                        <h4 class="color-gray standard-font">Destination: Homedeliverybr MIA</h4>
                    </div>
                </div>
                <div class="shipment-info">
                                    <div class="row mb-1 mt-3">
                    <div class="controls col-6">
                        <h4 class="standard-font color-gray">Shipment Info :</h4>
                    </div>
                    </div>
                    <div class="row d-none" id="calculator-items">
                        <livewire:calculator.items>
                    </div>
                </div>


            </div>
        </div>
        <div class="row p-3 shipment-info" >
            <div class="form-group col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Measuring Units') <span class="text-danger standard-font">*</span></label>
                    <div class="row mt-3">
                        <div class="col-6">
                            <input type="radio" value="ibs/in" name="unit" wire:model="unit" class="mr-1">
                            ibs/in
                        </div>
                        <div class="col-6">
                            <input type="radio" value="kg/cm" name="unit" wire:model="unit" class="mr-1">
                            kg/cm
                        </div>
                    </div>
                    @error('unit')
                    <div class="text-danger standard-font">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Weight') ({{ $unit == 'kg/cm'? 'kg': 'lbs' }})<span class="text-danger standard-font">*</span></label>
                    <input step="0.001" type="number" class="form-control" autocomplete="off" required name="weight" wire:model.debounce.500ms="weight" placeholder="">
                    <div class="help-block">
                        <span>{{ $weightOther }}</span>
                        @if ( $unit != 'kg/cm' )
                        <span>kg</span>
                        @else
                        <span>lbs</span>
                        @endif

                        @error('weight')
                        <div class="text-danger standard-font">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <input type="hidden" name="weight_discount" value="{{ $totalDiscountedWeight }}">
                <input type="hidden" name="discount_volume_weight" value="{{ $volumeWeight }}">
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Length') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }})<span class="text-danger standard-font">*</span></label>
                    <input step="0.001" name="length" type="number" class="form-control" autocomplete="off" required name="length" wire:model.debounce.500ms="length" placeholder="" />
                    <div class="help-block">
                        <span>{{ $lengthOther }}</span>
                        @if ( $unit != 'kg/cm' )
                        <span>cm</span>
                        @else
                        <span>in</span>
                        @endif
                        @error('length')
                        <div class="text-danger standard-font">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Width') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger standard-font">*</span></label>
                    <input step="0.001" type="number" name="width" class="form-control" autocomplete="off" required name="width" wire:model.debounce.500ms="width" placeholder="" />
                    <div class="help-block">
                        <span>{{ $widthOther }}</span>
                        @if ( $unit != 'kg/cm' )
                        <span>cm</span>
                        @else
                        <span>in</span>
                        @endif
                        @error('width')
                        <div class="text-danger standard-font">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Height') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger standard-font">*</span></label>
                    <input step="0.001" type="number" name="height" class="form-control" autocomplete="off" required name="height" wire:model.debounce.500ms="height" placeholder="" />
                    <div class="help-block">
                        <span>{{ $heightOther }}</span>
                        @if ( $unit != 'kg/cm' )
                        <span>cm</span>
                        @else
                        <span>in</span>
                        @endif
                        @error('height')
                        <div class="text-danger standard-font">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Order Value')  <span class="text-danger standard-font">*</span></label>
                    <input step="0.001" type="number" name="order_value" class="form-control" autocomplete="off" required name="order_value" wire:model.debounce.500ms="order_value" placeholder="" />
                    <div class="help-block"> 
                        @error('order_value')
                        <div class="text-danger standard-font">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div> -->
        </div>
        <div class="d-flex justify-content-between ">
            <div class="controls h2 mt-3 ml-3 ">
                <div class="shipment-info">
                    <label> @lang('parcel.The Rate will be applied on')
                        <br>
                        <div class="mt-2">
                            <strong class="text-success">{{ $volumeWeight }}
                                <span class="mx-1"> {{ $currentWeightUnit }} </span>
                                <span> ({{ $volumeWeightOther }} {{ $unitOther }}) </span>
                            </strong>
                        </div>
                    </label>
                </div>

            </div>
            <div class="row mt-3 mr-3">
                <button type="submit" class="btn btn-purple btn-md rounded px-5 my-3 standard-font color-gray">
                    Get Rates
                </button>
            </div>
        </div>
    </form>

</div>
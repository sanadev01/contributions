<div class="col-12 card-bg rounded p-4">
    <form action="{{action('CalculatorController@store')}}" method="POST">
        @csrf
        <div class="row">
                <input type="hidden" name="country_id" value="30">
                <input type="hidden" name="state_id" value="526">
            <!-- <div class="controls col-md-4" wire:ignore>
                <label>Destination Country</label>
                <select id="country" name="country_id" class="form-control selectpicker show-tick m-0 p-0" data-live-search="true">
                    <option value="">Select @lang('address.Country')</option>
                    @foreach (countries() as $country)
                    <option {{ old('country_id') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="controls col-md-4" wire:ignore>
                <label>Destination State</label>

                <option value="" selected disabled hidden>Select State</option>
                <select name="state_id" id="state" class="form-control selectpicker show-tick m-0 p-0" data-live-search="true">
                    <option value="">Select @lang('address.State')</option>
                </select>
            </div> -->
            <div class="form-group col-md-3">
                <div class="controls">
                    <label>@lang('parcel.Measuring Units') <span class="text-danger">*</span></label>
                    <div class="row mt-3">
                        <div class="col-4">
                            <input type="radio" value="ibs/in" name="unit" wire:model="unit" class="mr-1">
                            ibs/in
                        </div>
                        <div class="col-4">
                            <input type="radio" value="kg/cm" name="unit" wire:model="unit" class="mr-1">
                            kg/cm
                        </div>
                    </div>
                    @error('unit')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Weight') ({{ $unit == 'kg/cm'? 'kg': 'lbs' }})<span class="text-danger">*</span></label>
                    <input step="0.001" type="number" class="form-control" autocomplete="off" required name="weight" wire:model.debounce.500ms="weight" placeholder="">
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
                <input type="hidden" name="weight_discount" value="{{ $totalDiscountedWeight }}">
                <input type="hidden" name="discount_volume_weight" value="{{ $volumeWeight }}">
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Length') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }})<span class="text-danger">*</span></label>
                    <input step="0.001" name="length" type="number" class="form-control" autocomplete="off" required name="length" wire:model.debounce.500ms="length" placeholder="" />
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
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Width') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger">*</span></label>
                    <input step="0.001" type="number" name="width" class="form-control" autocomplete="off" required name="width" wire:model.debounce.500ms="width" placeholder="" />
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
            <div class="col-12 col-sm-6 col-md-2">
                <div class="controls">
                    <label>@lang('parcel.Height') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }}) <span class="text-danger">*</span></label>
                    <input step="0.001" type="number" name="height" class="form-control" autocomplete="off" required name="height" wire:model.debounce.500ms="height" placeholder="" />
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
        </div>
        <div class="d-flex justify-content-between">
            <div class="controls h2 mt-3">
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
            <div class="row mt-3 mr-1">
                <button type="submit" class="btn btn-blue btn-md rounded px-5 my-3">
                    Get Rates
                </button>
            </div>
        </div>

    </form>
</div>
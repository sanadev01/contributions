<div>
    <div class="row mb-1">
        <div class="form-group col-12">
            <div class="controls">
                <label>@lang('parcel.Measuring Units') <span class="text-danger">*</span></label>
                <select name="unit" class="form-control" required wire:model="unit">
                    <option value="" selected disabled hidden>Select Measuring Units</option>
                    <option value="lbs/in">lbs/in</option>
                    <option value="kg/cm">kg/cm</option>
                </select>
                @error('unit')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row mb-1">
        <div class="form-group col-12">
            <div class="controls">
                <label>@lang('parcel.Weight') ({{  $unit == 'kg/cm'? 'kg': 'lbs' }})<span class="text-danger">*</span></label>
                <input step="0.001" type="number" class="number-input" autocomplete="off" required name="weight" wire:model.debounce.500ms="weight"  placeholder="">
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
    </div>

    <div class="row mt-1">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="controls">
                <label>@lang('parcel.Length') ({{ $unit == 'kg/cm' ? 'cm' : 'in' }})<span class="text-danger">*</span></label>
                <input step="0.001" name="length" type="number" class="number-input" autocomplete="off" required name="length" wire:model.debounce.500ms="length" placeholder=""/>
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
                <input step="0.001" type="number" name="width" class="number-input" autocomplete="off" required name="width" wire:model.debounce.500ms="width" placeholder=""/>
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
                <input step="0.001" type="number" name="height" class="number-input" autocomplete="off" required name="height" wire:model.debounce.500ms="height" placeholder=""/>
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

    <div class="col-12 col-sm-12 col-md-12">
        <div class="controls h2 mt-2">
            <label> <span class="text-danger">*</span> @lang('parcel.The Rate will be applied on')  
                <strong class="text-danger h2">{{ $volumeWeight }} 
                    <span class="mx-1"> {{ $currentWeightUnit }} </span>
                    <span> ({{ $volumeWeightOther }} {{ $unitOther }}) </span>
                </strong>
            </label>
        </div>
    </div>

    @include('layouts.livewire.loading')
</div>

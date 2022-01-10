<div>
    <div class="border p-2 position-relative">
        <h3 class="bg-white shadow-sm p-2" data-toggle="collapse" data-target="#parcelCollapse"> @lang('orders.import-excel.Parcel')</h3>
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

                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('parcel.correios tracking code')<span class="text-danger"></span></label>
                        <input type="text" class="form-control" placeholder=""  name="correios_tracking_code" wire:model.defer.500ms="correios_tracking_code">
                        @error('customer_reference')
                            <div class="help-block text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
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


            <div class="row col-12 text-right">
                <div class="col-11 text-right">
                    @if(!$edit)
                        @if(!$order->error)
                        <div class="text-right">
                            <a href="{{ route('admin.import.import-excel.show', $order->import_id) }}" class="btn btn-success">
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
        </fieldset>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
</div>

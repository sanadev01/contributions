<div class="border p-2 position-relative">
    <h2 class="bg-white shadow-sm p-2" data-toggle="collapse" data-target="#itemsCollapse">@lang('orders.order-details.Order Items')</h2>
    <div id="itemsCollapse" class="collapse show">
        @foreach ($items as $keyId => $item)
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
                            <input type="number" readonly class="form-control total" value="{{ optional($item)['quantity'] * optional($item)['value']  }}">
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
                    <div class="form-group col-12 col-sm-4 col-md-4">
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
        <div class="row mt-3">
            <div class="col-12 text-right">
                <button class="btn btn-primary" wire:click="save">
                    @lang('orders.create.save')
                </button>
            </div>
        </div>
        <div wire:loading>
            <div class="position-absolute bg-white d-flex justify-content-center align-items-center w-100 h-100" style="top: 0; right:0;">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
</div>
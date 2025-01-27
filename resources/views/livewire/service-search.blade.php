<div class="position-relative">
 
    <input type="search" required  autocomplete="off" wire:focus="handleFocus()" wire:blur="handleBlur()" class="form-control" name="search" id="search" wire:model.debounce.500ms="search">
    <input type="hidden" name="declared_freight" id="declared_freight" value="{{ $selectedService?$this->getShippingRate($selectedService->id):null}}">
    <input type="hidden" name="shipping_service_id" id="us_shipping_service" value="{{optional($selectedService)->id}}">
    <input type="hidden" name="data-service-code" id="data-service-code" value="{{optional($selectedService)->service_sub_class}}">
    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    @error('us_shipping_service')
    <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    @error('user_declared_freight')
    <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 100">
        @if (!empty($dropDownServices))
        <div class="d-flex w-100 shadow-lg flex-column">
            @foreach ($dropDownServices as $service)
            <strong 
                class="w-100 p-2 border-bottom-light d-flex justify-content-between cursor-pointer"
                style="background-color: {{ $loop->odd ? '#f9f9f9' : '#ffffff' }}; color: #333;"
                wire:click="selectService('{{ $service['id'] }}')">
                <span> {{ $this->getShippingSubName($service['id']) }} </span>
                @if($order->recipient->country_id != 250||$shippingService->is_inbound_domestic_service)
                    <span>{{ $this->getShippingRate($service['id']) }} </span>
                @endif
            </strong>
            @endforeach
        </div>
        @elseif( strlen($search) && !$serviceId)
        <div class="w-100 shadow-lg text-center">
            No Results
        </div>
        @endif
    </div>
</div>
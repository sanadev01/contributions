<div class="items shadow p-4 border-top-success border-2 mt-2">
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Harmonized Code')<span class="text-danger"></span></label>
                    <livewire:components.search-sh-code class="form-control" required name="items[{{$keyId}}][sh_code]" :code="optional($item)['sh_code']"  :order="$order"/>
                @error("items.{$keyId}.sh_code")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Description') <span   id="feedback{{$keyId}}"></span></label>
                <input type="text" id="description{{$keyId}}" class="form-control descp" required name="items[{{$keyId}}][description]" max="500" min="0" onkeyup="descriptionChange({{$keyId}},this)" value="{{ optional($item)['description'] }}">
                <small id="characterCount{{$keyId}}" class="form-text text-muted"></small>

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
                <input type="number" class="form-control quantity" step="0.01" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');"  min="1" required name="items[{{$keyId}}][quantity]" value="{{ optional($item)['quantity'] }}" @if($order->products->isNotEmpty()) readonly @endif>
                @error("items.{$keyId}.quantity")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Unit Value') <span class="text-danger"></span></label>
                <input type="number" class="form-control value" step="0.01" min="0.01" required name="items[{{$keyId}}][value]" value="{{ optional($item)['value'] }}">
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
                <label class="d-flex">@lang('orders.order-details.order-item.Is Contains Dangrous Goods')  </label>
                <select name="items[{{$keyId}}][dangrous_item]" required class="form-control dangrous" id="dangrous_{{$keyId}}" onchange="change({{$keyId}})">
                    <option value="0">No</option>
                    <option value="contains_battery" {{ optional($item)['contains_battery'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Battery')</option>
                    <option value="contains_perfume" {{ optional($item)['contains_perfume'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Perfume')</option>
                    {{-- <option value="contains_flammable_liquid" {{ optional($item)['contains_flammable_liquid'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Flammable Liquid')</option> --}}
                </select>
                @error("items.{$keyId}.dangrous_item")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-4 text-right">
            <button class="btn btn-danger" type="button" role="button" wire:click="$emit('removeItem',{{$keyId}})">@lang('orders.order-details.order-item.Remove')</button>
        </div>
    </div>
</div>
<script>
    function descriptionChange(id,event){
            descriptionLength = event.value.length; 
            $('#feedback'+id).removeClass('text-success  text-danger');
            serviceCode = Number($('#shipping_service_id option:selected').attr('data-service-code'));
           
            var correios = <?php echo json_encode($correios); ?>; 
            var isCorreios = correios.indexOf(serviceCode) !== -1; 
            var geps = <?php echo json_encode($geps); ?>; 
            var isGeps = geps.indexOf(serviceCode) !== -1;

            var prime5 = <?php echo json_encode($prime5); ?>; 
            var isPrime5 = prime5.indexOf(serviceCode) !== -1;

            if(isPrime5){
                limit = 60;
            }else if(isGeps){
                limit = 50;
            }else if(isCorreios){
                limit = 500;
            }else{
                limit = 200;
            }   
            if(descriptionLength>limit)
            {
                $('#description'+id).val($('#description'+id).val().substr(0,limit));
                descriptionLength = limit;
            }

            $('#characterCount'+id).text(' '+descriptionLength+'/'+limit);
                if(descriptionLength<=50 && descriptionLength<limit){
                    
                    if(limit<=60&&descriptionLength>(limit/2))
                        updateFeedback('Good Description!',true,id) 
                    else 
                        updateFeedback('Weak Description!',false,id)  
                }
                else if(descriptionLength<=150 && descriptionLength<limit)
                    updateFeedback('Good Description!',true,id)
                else if(descriptionLength<=500 && descriptionLength<limit )
                    updateFeedback('Very Good Description!',true,id)        
                else
                    updateFeedback('Limit Exceeded!',false,id)
        }
        function updateFeedback(message,isValidFeedback,id) {
            $('#feedback'+id).addClass(isValidFeedback?'text-success':'text-danger');
            $('#feedback'+id).text(message);
        }  
</script>
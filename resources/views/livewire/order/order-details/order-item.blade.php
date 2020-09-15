<div class="items shadow p-4 shadow border-top-success border-2 mt-2">
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>Harmonized Code <span class="text-danger"></span></label>
                <livewire:components.search-sh-code class="form-control" required name="items[{{$keyId}}][sh_code]" :code="optional($item)['sh_code']" />
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>Description <span class="text-danger"></span></label>
                <input type="text" class="form-control" required name="items[{{$keyId}}][description]" value="{{ optional($item)['description'] }}">
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>Quantity <span class="text-danger"></span></label>
                <input type="number" class="form-control quantity" step="0.01" min="1" required name="items[{{$keyId}}][quantity]" wire:change="$emit('update-total')" value="{{ optional($item)['quantity'] }}">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>Unit Value <span class="text-danger"></span></label>
                <input type="number" class="form-control value" step="0.01" min="0.01" required wire:change="$emit('update-total')" name="items[{{$keyId}}][value]" value="{{ optional($item)['value'] }}">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>Total <span class="text-danger"></span></label>
                <input type="number" readonly class="form-control total" name="total" value="">
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label class="d-flex"> Is Contains Dangrous Goods </label>
                <select name="items[{{$keyId}}][dangrous_item]" class="selectpicker show-tick" id="">
                    <option value="0">No</option>
                    <option value="contains_battery" {{ optional($item)['contains_battery'] == 1 ? 'selected': '' }}>Contains Battery</option>
                    <option value="contains_perfume" {{ optional($item)['contains_perfume'] == 1 ? 'selected': '' }}>Contains Perfume</option>
                    <option value="contains_flammable_liquid" {{ optional($item)['contains_flammable_liquid'] == 1 ? 'selected': '' }}>Contains Flammable Liquid</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-4 text-right">
            <button class="btn btn-danger" type="button" role="button" wire:click="$emit('removeItem',{{$keyId}})">Remove</button>
        </div>
    </div>
</div>

@section('lvjs')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Run a callback when an event ("foo") is emitted from this component
        @this.on('update-total',function(){
            let id = @this.id;
            let quantity = document.querySelector(`[wire\\:id=${id}] .quantity`).value;
            let unitValue = document.querySelector(`[wire\\:id=${id}] .value`).value;
            let total = document.querySelector(`[wire\\:id=${id}] .total`);
            total.value = parseFloat(quantity) * parseFloat(unitValue);
        })
        
    })
</script>
@endsection
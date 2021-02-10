<div>
    @foreach ($parcels as $parcel)
        <div class="card-wrapper h-auto my-2 w-auto">
            <input class="c-card" type="checkbox" name="parcels[]" id="{{$parcel->id}}" {{ in_array( $parcel->id, old('parcels',$selected) ) ? 'checked':'' }} value="{{$parcel->id}}">
            <div class="card-content">
                <div class="card-state-icon"></div>
                <label for="{{$parcel->id}}">
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.User'):</strong> <span class="text-info">{{ optional($parcel->user)->name }} {{ optional($parcel->user)->last_name }}</span>
                    </div>
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.Merchant'):</strong> <span class="text-info">{{ $parcel->merchant }}</span>
                    </div>
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.Customer Refrence'):</strong> <span class="text-info">{{ $parcel->customer_reference }}</span>
                    </div>
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.Tracking ID'):</strong> <span class="text-info">{{ $parcel->tracking_id }}</span>
                    </div>
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.WHR')#</strong> <span class="text-info">{{  $parcel->warehouse_number }}</span>
                    </div>
                    <div class="h5 py-1 px-2">
                        <strong class="border-bottom-dark mr-2">@lang('invoice.weight')</strong> <span class="text-info">{{ $parcel->getOriginalWeight('kg') }} Kg  ( {{ $parcel->getOriginalWeight('lbs') }} lbs )</span>
                    </div>
                </label>
            </div>
        </div>
    @endforeach

    @include('layouts.livewire.loading')
</div>

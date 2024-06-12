@extends('admin.consolidation.wizard')

@section('wizard-form')

<form action="{{ route('admin.consolidation.parcels.services.index',$parcel) }}" method="POST">
    @csrf
    <fieldset role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current p-4" aria-hidden="false">
        <div class="row justify-content-center">
            <div class="col-md-8 col-12">
                <h4>@lang('orders.services.handling-services')</h4>
                <hr>
            </div>
        </div>
        <div class="row py-5 justify-content-center">
            <div class="col-md-8 col-12">
                @foreach ($services as $service)
                    <div class="vs-checkbox-con vs-checkbox-primary my-3">
                        <input type="checkbox" name="services[]" {{ in_array($service->id,$parcel->services->pluck('service_id')->toArray()) ? 'checked':'' }} value="{{$service->id}}"> 
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span> 
                        <span> {{ $service->name }} <strong>  ( Custo  extra  {{ $service->price }})</strong>  USD por envio
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-md-12">
                <a href="{{ route('admin.consolidation.parcels.edit',$parcel->encrypted_id) }}" class="btn btn-primary btn-lg pull-left">@lang('consolidation.Previous')</a>
                <button class="btn btn-primary btn-lg pull-right">@lang('consolidation.Save')</button>
            </div>
        </div>
    </fieldset>
</form>

@endsection
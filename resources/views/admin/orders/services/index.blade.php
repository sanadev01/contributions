@extends('admin.orders.layouts.wizard')

@section('wizard-form')
<form action="{{ route('admin.orders.services.store',$order) }}" method="POST" class="wizard">
    @csrf
    <div class="content clearfix">
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">@lang('orders.services.Step 1')</h6>
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
                            <input type="checkbox" name="services[]" {{ in_array($service->id,$order->services->pluck('service_id')->toArray()) ? 'checked':'' }} value="{{$service->id}}"> 
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
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                <a href="{{ route('admin.orders.recipient.index',$order) }}" role="menuitem">@lang('orders.services.Previous')</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">@lang('orders.services.Next')</button>
            </li>
        </ul>
    </div>
</form>
@endsection
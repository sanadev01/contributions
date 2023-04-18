@extends('admin.orders.layouts.wizard')

@section('wizard-form')
<form action="{{ route('admin.orders.services.store',$order) }}" method="POST" class="wizard">
    @csrf
    <div class="content clearfix">
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">@lang('orders.services.Step 1')</h6>
        <fieldset role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current p-4 bg-white" aria-hidden="false">
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
                            <input type="checkbox" id="additional_service" name="services[]" {{ in_array($service->id,$order->services->pluck('service_id')->toArray()) ? 'checked':'' }} value="{{$service->id}}" data-service="{{ $service->name}}"> 
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span> 
                            <span> {{ $service->name }} <strong>  ( Custo  extra @if($service->name == 'Insurance' || $service->name == 'Seguro') maximum 3% of total declared value of order items and minimum @endif{{ $service->price }})</strong>  USD por envio
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled mt-2" aria-disabled="true">
                <a href="{{ route('admin.orders.order-details.index',$order->encrypted_id) }}" role="menuitem">@lang('orders.services.Previous')</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary mt-2">@lang('orders.services.Next')</button>
            </li>
        </ul>
    </div>
</form>

<div class="modal fade" id="disclaimer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h4 class="modal-title text-dark" id="exampleModalLabel">@lang('orders.order-details.Disclaimer')</h4>
        </div>
        <div class="modal-body">
            <h5 class="text-justify text-danger">@lang('orders.order-details.Disclaimer Text')</h5>
        </div>
        <div class="modal-footer">
          <button type="button" id="close-btn" class="btn btn-danger">Close</button>
          <button type="button" id="agree-btn" class="btn btn-success">Agree</button>
        </div>
      </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $('#additional_service').change(function() {
        if($(this).is(':checked')){
            let service_name = $(this).data('service');
            if(service_name == 'Insurance' || service_name == 'Seguro')
            {
                $('#disclaimer').modal({
                    show : true,
                    backdrop: 'static',
                    keyboard: false
                })

                $('#agree-btn').on('click',function(){
                    $('#disclaimer').modal('hide')
                });

                $('#close-btn').on('click',function(){
                    $('#additional_service').prop('checked', false);
                    $('#disclaimer').modal('hide')
                });
            }
        }
    });

</script>
@endsection
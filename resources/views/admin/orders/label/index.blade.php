@extends('layouts.master')

@section('page')
<div class="card">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-form"></h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary pull-right">
            @lang('shipping-rates.Return to List')
        </a>

        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="label-wrapper p-5">
                <div class="d-flex justify-content-center align-items-center h1 flex-column">
                    <p><i class="fa fa-spinner fa-spin"></i></p>
                    <p class="mt-1">Loading Label...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
        
          <h4 class="modal-title">Confirm!</h4>
        </div>
        <div class="modal-body">
          <p><h5>@lang('orders.update-label')</h5></p>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="updateLabel({{$order->id}},'#row_{{$order->id}}')">Yes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')

    <script>
        function loadLabel(updateLabel){
            setTimeout(function(){
                $.post('{{ route("admin.orders.label.store",$order) }}',{
                    update_label: updateLabel
                })
                .done(function(response){
                    window.labelLoader  = $('.label-wrapper').html();
                    $('.label-wrapper').html(response)
                })
                .fail(function(error){
                    console.log(error)
                })
            },2000)
        }

        function reloadLabel(){
            $('.label-wrapper').html(window.labelLoader);
            loadLabel(false);
        }

        function updateLabel(){
            $('#confirm').modal('hide');
            $('.label-wrapper').html(window.labelLoader);
            loadLabel(true);
        }

        loadLabel(false);


    </script>

@endsection

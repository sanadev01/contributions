@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Commission Reports</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <livewire:reports.commission-show :user="$user"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <x-modal/>
@endsection
@section('js')
    <script>
        $('body').on('change','#bulk-actions',function(){
            if ( $(this).val() == 'clear' ){
                $('.bulk-sales').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-sales').prop('checked',true)
            }else if ( $(this).val() == 'pay-commission' ){
                var orderIds = [];
                $.each($(".bulk-sales:checked"), function(){
                    orderIds.push($(this).val());
                    
                    // $(".result").append('HD-' + this.value + ',');
                });
                
                $('#bulk_sale_form #command').val('pay-commission');
                $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
                $('#confirm').modal('show');
                // $('#bulk_sale_form').submit();
            }
        })
    </script>
  
@endsection
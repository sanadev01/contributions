@extends('layouts.master')

@section('page')
<livewire:container.container />
@endsection

@section('js')
    <script>
        $('body').on('change','#bulk-actions',function(){
            if ( $(this).val() == 'clear' ){
                $('.bulk-container').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-container').prop('checked',true)
            }else if ( $(this).val() == 'assign-awb' ){
                var containerIds = [];
                $.each($(".bulk-container:checked"), function(){
                    containerIds.push($(this).val());
                    
                    // $(".result").append('HD-' + this.value + ',');
                });
                
                $('#bulk_sale_form #command').val('assign-awb');
                $('#bulk_sale_form #data').val(JSON.stringify(containerIds));
                $('#confirm').modal('show');
                // $('#bulk_sale_form').submit();
            }
        })
    </script>
@endsection

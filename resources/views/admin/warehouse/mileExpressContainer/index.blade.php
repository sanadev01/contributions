@extends('layouts.master')

@section('page')
    <livewire:mile-express.container>
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
                });
                
                $('#bulk_sale_form #command').val('assign-awb');
                $('#bulk_sale_form #data').val(JSON.stringify(containerIds));
                $('#confirm').modal('show');
            }
        })

        var myObject = {
            foo: "bar",

            func: function() {
                var self = this;
                console.log("outer func:  this.foo = " + this.foo);
                console.log("outer func:  self.foo = " + self.foo);
                (function() {
                    console.log("inner func:  this.foo = " + this.foo);
                    console.log("inner func:  self.foo = " + self.foo);
                }());
            }
        };

        myObject.func();
    </script>
@endsection
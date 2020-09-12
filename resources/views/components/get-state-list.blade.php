    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#country').on('change', function(){
            
            let country_id = $(this).val()
            $.ajax({ 
               type: 'POST',
               url: "{{route('admin.ajax.state')}}",
               data: {country_id: country_id},
               success: function (data){

                    $("#state").empty();
                    $("#state").append('<option value="">Select @lang("address.State")</option>');
                    $.each(data,function(key,value){
                        $("#state").append('<option value="'+key+'">'+value+'</option>');
                    });
               }, 
               error: function(e) {
                    console.log(e);
               }
            });
         });

    </script>
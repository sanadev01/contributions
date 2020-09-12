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
                $.each(data,function(key,value){
                    $("#state").selectpicker('refresh');
                    $("#state").append('<option value="'+key+'">'+value+'</option>');
                });
           }, 
           error: function(e) {
                console.log(e);
           }
        });
     });
</script>
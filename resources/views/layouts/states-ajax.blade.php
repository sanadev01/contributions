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
                $.each(data,function(index,state){
                    $("#state").append('<option value="'+state.id+'">'+state.code+'</option>');
                });
                $("#state").selectpicker('refresh');
           }, 
           error: function(e) {
                console.log(e);
           }
        });
     });
</script>
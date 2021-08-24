<script>
    $('#country').on('change', function(){
        
        let country_id = $(this).val()
        if(country_id == 46)
        {
          return getChileRegions();
        }
          $.ajaxSetup({
               headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
          });
        $.ajax({ 
           type: 'POST',
           url: "{{route('admin.ajax.state')}}",
           data: {country_id: country_id},
           success: function (data){
                $("#state").html("<option value=''>No Data</option>")
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

    function getChileRegions()
     {    
          $.get('{{ route("api.orders.recipient.chile_regions") }}')
          .then(function(response){
               if(response.success == true)
               {
                    $.each(response.data,function(key, value)
                    {
                         $("#state").append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                         $('#state').selectpicker('refresh');
                    });
               }else {
                    toastr.error(response.message)
               }
          }).catch(function(error){
               console.log(error);
          })
     }
</script>
<script>
    $('#origin_country').on('change', function(){
        let country_id = $(this).val();

        if(country_id == 46){
            return getChileRegions();
        }

        return getStates(country_id, 'sender');
    });

    $('#destination_country').on('change', function(){
        let country_id = $(this).val();

        if(country_id == 46){
            return getChileRegions();
        }

        return getStates(country_id, 'recipient');
    });


    function getStates(country_id, typeOfAddress){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: 'ajax/get-states',
            data: {country_id: country_id},
            success: function(data){

                if (typeOfAddress == 'sender') {
                    $('#sender_state').html("<option value=''>No Data</option>");
                    $.each(data, function(index, state){
                        $('#sender_state').append('<option value="'+state.code+'">'+state.code+'</option>');
                    });
                    $('#sender_state').selectpicker('refresh');
                }

                if (typeOfAddress == 'recipient') {
                    $('#recipient_state').html("<option value=''>No Data</option>");
                    $.each(data, function(index, state){
                        $('#recipient_state').append('<option value="'+state.code+'">'+state.code+'</option>');
                    });
                    $('#recipient_state').selectpicker('refresh');
                }
                
                
            },
            error: function(data){
                console.log(data);
            }
        });
    }

    function getChileRegions()
      {    
           $.get('{{ route("api.correios-chile-regions") }}')
           .then(function(response){
                if(response.success == true)
                {
                     $.each(response.data,function(key, value)
                     {
                          $("#recipient_state").append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                          $('#recipient_state').selectpicker('refresh');
                     });
                }else {
                     toastr.error(response.message)
                }
           }).catch(function(error){
                console.log(error);
           })
      }

</script>
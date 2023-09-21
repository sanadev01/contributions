<script>
     $('#country').on('change', function(){
         
         let country_id = $('#country').val()
         if(country_id == 46)
         {
           return getChileRegions();
         }
 
           return getStates();
           
      });
 
      $('#country').ready(function() {
         
         let country_id = $('#country').val()
         if(country_id == 46)
         {
           return getChileRegions();
         }
 
           return getStates();
           
      });
 
      function getChileRegions()
      {    
           $.get('{{ route("api.correios-chile-regions") }}')
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
 
      function getStates()
      {
           const old_state = $('#state').val();
           $.ajaxSetup({
                headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
           });
           $.ajax({ 
            type: 'POST',
            url: "{{route('admin.ajax.state')}}",
            data: {country_id:  $('#country').val()},
            success: function (data){
                $("#state").html("<option value=''>No Data</option>")
                $.each(data,function(index,state){
                     $("#state").append('<option value="'+state.id+'">'+state.code+'</option>');
                });
                 $("#state").selectpicker('refresh');
                if(old_state != undefined || old_state != '')
                {
                     $('#state').val(old_state);
                     $('#state').selectpicker('val', old_state);
                }
            }, 
            error: function(e) {
                 console.log(e);
            }
         });
      }
 </script>
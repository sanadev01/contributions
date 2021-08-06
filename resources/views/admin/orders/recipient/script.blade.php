<script>
    $(document).ready(function(){
        $('#accountType').on('change', function(){
            let val = $(this).val();
            if(val == 'individual'){
                $('#cpf_label_id').css('display', 'block')
                $('#cnpj_label_id').css('display', 'none')
                $('#tax_id').attr('placeholder', 'CPF')
            }else{
                $('#cpf_label_id').css('display', 'none')
                $('#cnpj_label_id').css('display', 'block')
                $('#tax_id').attr('placeholder', 'CNPJ')
            }
        })
    })

    $('#address_id').on('change',function(){
        if ( $(this).val() == undefined || $(this).val() == "" ) return;
        $('#loading').fadeIn();
        $.post('{{ route("api.orders.recipient.update") }}',{
            address_id: $(this).val(),
            order_id: {{ $order->id }}
        })
        .then(function(response){
            if ( response.success ){
                window.location.reload();
            }else{
                $('#loading').fadeOut();
                toastr.error(response.message)
            }

        }).catch(function(error){
            $('#loading').fadeOut();
        })
    })
    
    $('#zipcode').on("change", function(){
        let country_id = $("#country").val();
        if(country_id == '30')
        {
            if ( $(this).val() == undefined || $(this).val() == "" ) return;
            $('#loading').fadeIn();
            $.get('{{ route("api.orders.recipient.zipcode") }}',{
                zipcode: $(this).val(),
            })
            .then(function(response){
                console.log(response.data);
                if ( response.success ){
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append("<p><b>According to your zipcode, your address should be this</b></p><p><span style='color: red;'>Address: </span><span>"+response.data.street+"</span></p><p><span style='color: red;'>City: </span><span>"+response.data.city+"</span></p><p><span style='color: red;'>State: </span><span>"+response.data.uf+"</span></p>");
                }else{
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                    toastr.error(response.message)
                }
            
            }).catch(function(error){
                $('#loading').fadeOut();
            })
        }
    })

    $(document).ready(function(){
        let old_city = $('#commune').data('value');
        // For getting Chile Regions
        $('#country').ready(function() {
            $('#regions_response').css('display', 'none');
            let val = $('#country').val();
            const old_region = $('#region').data('value');

            if(val == '46'){
                $('#cpf').css('display', 'none')
                $('#div_state').css('display', 'none')
                $('#div_city').css('display', 'none')
                $('#div_street_number').css('display', 'none')

                $('#div_region').css('display', 'block')
                $('#div_communes').css('display', 'block')

                $('#label_address').css('display', 'none')
                $('#label_chile_address').css('display', 'inline-block')

                $('#state').prop('disabled', true);
                $('#city').attr('disabled', true);

                $('#region').prop('disabled', false);
                $('#commune').attr('disabled', false);

                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.chile_regions") }}')
                .then(function(response){
                    if(response.success == true)
                    {
                        $('#region').attr('disabled', false);
                        $.each(response.data,function(key, value)
                        {
                            $('#region').append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                            $('#region').selectpicker('refresh');
                            if(old_region != undefined || old_region != '')
                            {
                                $('#region').val(old_region);
                            }
                        });
                        $('#loading').fadeOut();
                    }else{
                        $('#loading').fadeOut();
                        $('#regions_response').css('display', 'block');
                        $('#regions_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                    
                }).catch(function(error){
                    console.log(error);
                })
                // Fetch Communes
                if(old_region != undefined || old_region != '')
                {
                    $('#loading').fadeIn();
                    $('#communes_response').css('display', 'none');
                    $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                        region_code: old_region,
                    })
                    .then(function(response){
                        if(response.success == true)
                        {
                            $('#commune').attr('disabled', false);
                            $.each(response.data,function(key, value)
                            {
                                $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                                $('#commune').selectpicker('refresh');
                                if(old_city != undefined || old_city != '')
                                {
                                    $('#commune').val(old_city);
                                }
                            });
                            $('#loading').fadeOut();
                        }else{
                            $('#loading').fadeOut();
                            $('#communes_response').css('display', 'block');
                            $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                            toastr.error(response.message)
                        }
                    }).catch(function(error){
                        console.log(error);
                    })
                }    

            }else {
                $('#cpf').css('display', 'block')
                $('#div_state').css('display', 'block')
                $('#div_city').css('display', 'block')
                $('#div_street_number').css('display', 'block')

                $('#div_region').css('display', 'none')

                $('#label_address').css('display', 'inline-block')
                $('#label_chile_address').css('display', 'none')

                $('#state').prop('disabled', false);
                $('#city').attr('disabled', false);

                $('#region').prop('disabled', true);
                $('#commune').attr('disabled', true);
            }
        });

        $('#country').on('change', function(){
            $('#regions_response').css('display', 'none');
            let val = $(this).val();
            const old_region = $('#region').data('value');

            if(val == '46'){
                $('#cpf').css('display', 'none')
                $('#div_state').css('display', 'none')
                $('#div_city').css('display', 'none')
                $('#div_street_number').css('display', 'none')

                $('#div_region').css('display', 'block')
                $('#div_communes').css('display', 'block')

                $('#label_address').css('display', 'none')
                $('#label_chile_address').css('display', 'inline-block')

                $('#state').prop('disabled', true);
                $('#city').attr('disabled', true);

                $('#region').prop('disabled', false);
                $('#commune').attr('disable', false);

                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.chile_regions") }}')
                .then(function(response){
                    if(response.success == true)
                    {
                        $('#region').attr('disabled', false);
                        $.each(response.data,function(key, value)
                        {
                            $('#region').append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                            $('#region').selectpicker('refresh');
                            if(old_region != undefined || old_region != '')
                            {
                                $('#region').val(old_region);
                            }
                        });
                    }else {
                        $('#loading').fadeOut();
                        $('#regions_response').css('display', 'block');
                        $('#regions_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                   console.log(error);
                })
                // Fetch Communes
                if(old_region != undefined || old_region != '')
                {
                    $('#loading').fadeIn();
                    $('#communes_response').css('display', 'none');
                    $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                        region_code: old_region,
                    })
                    .then(function(response){
                        if(response.success == true)
                        {
                            $('#commune').attr('disabled', false);
                            $.each(response.data,function(key, value)
                            {
                                $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                                $('#commune').selectpicker('refresh');
                            });
                            if(old_city != undefined || old_city != '')
                            {
                                $('#commune').val(old_city);
                            }
                            $('#loading').fadeOut();
                        }else{
                            $('#loading').fadeOut();
                            $('#communes_response').css('display', 'block');
                            $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                            toastr.error(response.message)
                        }
                    }).catch(function(error){
                        console.log(error);
                    })
                }

            }else {
                $('#cpf').css('display', 'block')
                $('#div_state').css('display', 'block')
                $('#div_city').css('display', 'block')
                $('#div_street_number').css('display', 'block')

                $('#div_region').css('display', 'none')
                $('#div_communes').css('display', 'none')

                $('#label_address').css('display', 'inline-block')
                $('#label_chile_address').css('display', 'none')

                $('#state').prop('disabled', false);
                $('#city').attr('disabled', false);

                $('#region').prop('disabled', true);
                $('#commune').attr('disable', true);
            }
        });

        // For getting Chile Communes based on selected region
        $('#region').on('change', function(){
            const old_region = $('#region').data('value');
            $('#communes_response').css('display', 'none');
            if ( $(this).val() == undefined || $(this).val() == "" ) return;
            let region_code = $('#region').val();
            
            $('#loading').fadeIn();
            $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                region_code: $(this).val(),
            })
            .then(function(response){
                if(response.success == true)
                {
                    $('#commune').attr('disabled', false);
                    $.each(response.data,function(key, value)
                    {
                        $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                        $('#commune').selectpicker('refresh');
                    });
                    if((old_region != undefined || old_region != '') && (old_city != undefined || old_city != '') && region_code == old_region)
                    {
                        $('#commune').val(old_city);
                    }else{
                        $('#commune').val('');
                    }
                    $('#loading').fadeOut();
                }else{
                    $('#loading').fadeOut();
                    $('#communes_response').css('display', 'block');
                    $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                    toastr.error(response.message)
                }
            }).catch(function(error){
                console.log(error);
            })
        });

        // For validating Address and Zipcode
        $('#commune').on('change', function(){
            let commune = $(this).val();
            let address = $('#address').val();
            // let street_no = $('#street_no').val();
            let country = $('#country').val();
            // let direction = address.concat(" ",street_no);
            
            if ( address == undefined || address == "" ) return;

            if(country == '46')
            {
                $('#loading').fadeIn();

                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    address: address,
                })
                .then(function(response){
                    if ( response.success == true && response.data.cpostal != 0){
                        $('#loading').fadeOut();
                        $('#zipcode').val(response.data.cpostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.cpostal);
                    }else if(response.success == true && response.data.cpostal == 0)
                    {
                        $('#zipcode_response').empty().append("<p style='color: red;'><b>According to your Coummune, your address or street is Invalid</b></p><p><span style='color: red;'>zipcode: </span><span>");
                        $('#loading').fadeOut();
                    }
                    else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });

        $('#address').on('change', function(){
            let address = $(this).val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            if(country == '46' && commune != undefined && commune != "" && address.length > 5)
            {
                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    address: address,
                })
                .then(function(response){
                    if ( response.success == true && response.data.cpostal != 0){
                        $('#zipcode').val(response.data.cpostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.cpostal);
                        $('#loading').fadeOut();
                    }
                    else if(response.success == true && response.data.cpostal == 0)
                    {
                        $('#zipcode_response').empty().append("<p style='color: red;'><b>According to your Coummune, your address or street is Invalid</b></p><p><span style='color: red;'>zipcode: </span><span>");
                        $('#loading').fadeOut();
                    }
                    else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });

        $('#street_no').on('change', function(){
            let address = $('#address').val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let street_no = $(this).val();
            let direction = address.concat(" ",street_no);

            if(country == '46' && commune != undefined && commune != "" && address.length > 5 && street_no.length > 0 && direction.length > 5)
            {
                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    direction: direction,
                })
                .then(function(response){
                    if ( response.success == true && response.data.cpostal != 0){
                        $('#zipcode').val(response.data.cpostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.cpostal);
                        $('#loading').fadeOut();
                    }
                    else if(response.success == true && response.data.cpostal == 0)
                    {
                        $('#zipcode_response').empty().append("<p style='color: red;'><b>According to your Coummune, your address or street is Invalid</b></p><p><span style='color: red;'>zipcode: </span><span>");
                        $('#loading').fadeOut();
                    }
                    else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });
    })

    // USPS Logics

    $(document).ready(function(){
        $('#address').on('change', function(){
            window.validate_us_address();
        });

        $('#country').on('change', function() {
            window.validate_us_address();

            if($('#country').val() == '250'){
                $('#div_street_number').css('display', 'none')
            }else{
                $('#div_street_number').css('display', 'block')
            }
        });

        $('#country').ready(function() {
            if($('#country').val() == '250'){
                $('#div_street_number').css('display', 'none')
            }else{
                $('#div_street_number').css('display', 'block')
            }
        });

        $('#state').on('change', function() {
            window.validate_us_address();
        });

        $('#city').on('change', function() {
            window.validate_us_address();
        });

    })

    validate_us_address = function()
    {
        let country = $('#country').val();
        let address = $('#address').val();
        let state = $('#state option:selected').text();
        let city = $('#city').val();

        if(country == '250' && state != undefined && address.length > 4 && city.length >= 4)
            {
                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.us_address") }}',{
                    address: address,
                    state: state,
                    city: city,
                }).then(function(response){
                    
                    if ( response.success == true && response.zipcode != 0){
                        $('#loading').fadeOut();
                        $('#zipcode').val(response.zipcode);
                        $('#zipcode_response').empty().append("<p><b>According to your given Addrees, your zip code should be this</b></p><p><span style='color: red;'>Zipcode: </span><span>"+response.zipcode+"</span></p>");
                    }else {
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'><b>According to USPS,</b></p><p><span style='color: red;'></span><span>"+response.message+"</span></p>");
                    }

                }).catch(function(error){
                    console.log(error);
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append("<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
                })
            }
    }

 
</script>
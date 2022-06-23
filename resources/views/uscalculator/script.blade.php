<script>
   
   $(document).on('click', 'input[type="checkbox"]', function() {      
        $('input[type="checkbox"]').not(this).prop('checked', false);      
    });

    $(document).ready(function(){
        if ($('#from_herco').is(':checked')) {
            
            $('#to_herco').prop('checked', false);

            $('#origin').removeClass('d-none');
            $('#destination').addClass('d-none');

            $('#recipient_info').removeClass('d-none');
            $('#sender_info').addClass('d-none');

            window.toggleSenderInputs();
        }

        if ($('#to_herco').is(':checked')) {
            
            $('#from_herco').prop('checked', false);

            $('#origin').addClass('d-none');
            $('#destination').removeClass('d-none');

            $('#sender_info').removeClass('d-none');

            $('input[name^="items"]').prop('disabled', true);
            window.toggleRecipientInputs();
        }

        $('#from_herco').change(function(){
            if($(this).is(':checked')){
                $('#to_herco').prop('checked', false);

                $('#origin').removeClass('d-none');
                $('#destination').addClass('d-none');

                $('#recipient_info').removeClass('d-none');
                $('#sender_info').addClass('d-none');

                window.toggleSenderInputs();
            }
        });

        $('#to_herco').change(function(){
            if($(this).is(':checked')){
                $('#from_herco').prop('checked', false);

                $('#origin').addClass('d-none');
                $('#destination').removeClass('d-none');

                $('#sender_info').removeClass('d-none');
                $('#recipient_info').addClass('d-none');

                $('input[name^="items"]').prop('disabled', true);
                window.toggleRecipientInputs();
            }
        });

        $('#destination_country').on('change', function(){
            let country_id = $(this).val();
            
            if (country_id == '250') {
                $('#calculator-items').removeClass('d-block');
                $('input[name^="items"]').val('');
                $('input[name^="items"]').prop('disabled', true);

                window.validateRecipientAddress();
            }

            if (country_id != '250') {
                $('#calculator-items').addClass('d-block');
                $('input[name^="items"]').prop('disabled', false);
            }

            return getStates(country_id, 'recipient');
        });

        $('#recipient_state').on('change', function() {
            window.validateRecipientAddress();
        });

        $('#recipient_city').on('change', function() {
            window.validateRecipientAddress();
        });

        $('#recipient_address').on('change', function() {
            window.validateRecipientAddress();
        });

        $('#origin_country').on('change', function(){
            if ($(this).val() == '250') {
                window.validateSenderAddress();
            }
        });

        $('#sender_state').on('change', function() {
            window.validateSenderAddress();
        });

        $('#sender_city').on('change', function() {
            window.validateSenderAddress();
        });

        $('#sender_address').on('change', function() {
            window.validateSenderAddress();
        });

    });

    toggleSenderInputs = function () {
        $('#origin_country').prop('disabled', true);
        $('#sender_state').prop('disabled', true);
        $('#sender_city').prop('disabled', true);
        $('#sender_zipcode').prop('disabled', true);
        $('#sender_address').prop('disabled', true);

        $('#origin_country').prop('required', false);
        $('#sender_state').prop('required', false);
        $('#sender_city').prop('required', false);
        $('#sender_zipcode').prop('required', false);
        $('#sender_address').prop('required', false);

        $('#destination_country').prop('disabled', false);
        $('#destination_country').selectpicker('refresh');
        $('#recipient_state').prop('disabled', false);
        $('#recipient_state').selectpicker('refresh');
        $('#recipient_city').prop('disabled', false);
        $('#recipient_zipcode').prop('disabled', false);
        $('#recipient_address').prop('disabled', false);

        $('#destination_country').prop('required', true);
        $('#recipient_state').prop('required', true);
        $('#recipient_city').prop('required', true);
        $('#recipient_zipcode').prop('required', true);
        $('#recipient_address').prop('required', true);
    }

    toggleRecipientInputs = function () {
        $('#origin_country').prop('disabled', false);
        $('#sender_state').prop('disabled', false);
        $('#sender_city').prop('disabled', false);
        $('#sender_zipcode').prop('disabled', false);
        $('#sender_address').prop('disabled', false);

        $('#origin_country').prop('required', true);
        $('#origin_country').selectpicker('refresh');
        $('#sender_state').prop('required', true);
        $('#sender_state').selectpicker('refresh');
        $('#sender_city').prop('required', true);
        $('#sender_zipcode').prop('required', true);
        $('#sender_address').prop('required', true);

        $('#destination_country').prop('disabled', true);
        $('#recipient_state').prop('disabled', true);
        $('#recipient_city').prop('disabled', true);
        $('#recipient_zipcode').prop('disabled', true);
        $('#recipient_address').prop('disabled', true);

        $('#destination_country').prop('required', false);
        $('#recipient_state').prop('required', false);
        $('#recipient_city').prop('required', false);
        $('#recipient_zipcode').prop('required', false);
        $('#recipient_address').prop('required', false);
    }

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

    validateRecipientAddress = function(){
        let country = $('#destination_country').val();
        let address = $('#recipient_address').val();
        let state = $('#recipient_state option:selected').text();
        let city = $('#recipient_city').val();

        if(country == '250' && state != undefined && address.length > 4 && city.length >= 4){
            window.validateUSAddress(country, address, state, city, 'recipient');
        }
    }

    validateSenderAddress = function(){
        let country = $('#origin_country').val();
        let address = $('#sender_address').val();
        let state = $('#sender_state option:selected').text();
        let city = $('#sender_city').val();

        if(country == '250' && state != undefined && address.length > 4 && city.length >= 4){
          window.validateUSAddress(country, address, state, city, 'sender');
        }
    }

    validateUSAddress = function(country, address, state, city, type)
    {
        if(country == '250' && state != undefined && address.length > 4 && city.length >= 4)
        {
            $('#loading').fadeIn();
            $.get('{{ route("api.orders.recipient.us_address") }}',{
                address: address,
                state: state,
                city: city,
            }).then(function(response){
                
                if ( response.success == true && response.zipcode != 0)
                {
                    if (type == 'sender') {
                        $('#sender_zipcode').val(response.zipcode);
                        $('#sender_zipcode_response').empty().append("<p style='color: #198754;'>According to your given Addrees, your zip code should be: <span>"+response.zipcode+"</span></p>");
                    }

                    if (type == 'recipient') {
                        $('#recipient_zipcode').val(response.zipcode);
                        $('#recipient_zipcode_response').empty().append("<p style='color: #198754;'>According to your given Addrees, your zip code should be: <span>"+response.zipcode+"</span></p>");
                    }

                }else {
                    if (type == 'sender') {
                        $('#sender_zipcode_response').empty().append("<p style='color: red;'><span>"+response.message+"</span></p>");
                    }

                    if(type == 'recipient'){
                        $('#recipient_zipcode_response').empty().append("<p style='color: red;'><span>"+response.message+"</span></p>");
                    }
                }
            }).catch(function(error){
                if (type == 'sender') {
                    $('#sender_zipcode_response').empty().append("<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
                }

                if (type == 'recipient') {
                    $('#recipient_zipcode_response').empty().append("<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
                }
                
            })
        }
    }

</script>
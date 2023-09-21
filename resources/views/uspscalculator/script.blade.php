<script>
    $('#origin_country option:contains("United States")').prop('selected',true);

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

    $('#destination_country').on('change', function(){
        if ($(this).val() == '250') {
            $('#calculator-items').removeClass('d-block');
            $('input[name^="items"]').val('');
            $('input[name^="items"]').prop('disabled', true);
            window.validateRecipientAddress();
        }

        if ($(this).val() != '250') {
            $('#calculator-items').addClass('d-block');
            $('input[name^="items"]').prop('disabled', false);
        }
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

    validateSenderAddress = function(){

        let country = $('#origin_country').val();
        let address = $('#sender_address').val();
        let state = $('#sender_state option:selected').text();
        let city = $('#sender_city').val();

        if(country == '250' && state != undefined && address.length > 4 && city.length >= 4){
          window.validateUSAddress(country, address, state, city, 'sender');
        }
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
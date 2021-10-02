<script>
    
    let total_price = $('#total_price').val();
    if(total_price > 0)
    {
        $('#calculated_rates').empty().append("<h4 class='text-danger'>Total Charges :" +total_price+ " USD </h4>");
        $('#submitBtn').prop('disabled', false);
    }

    $('#sender_state').on('change', function() {
        resetErrorMessages();
        window.validate_us_address();
    });

    $('#sender_city').on('change', function() {
        resetErrorMessages();
        window.validate_us_address();
    });

    $('#sender_address').on('change', function() {
        resetErrorMessages();
        window.validate_us_address();
    });

    $('#usps_shipping_service').ready(function() {
        
        resetErrorMessages();

        if($('#sender_state').val() == '' || $('#sender_state').val() == undefined)
        {
            $('#state_error').empty().append("<p style='color: red;'>state must be selected</p>");

            return false;
        }

        if($('#sender_address').val() == '' || $('#sender_address').val() == undefined)
        {
            $('#address_error').empty().append("<p style='color: red;'>complete street address must be mention</p>");

            return false;
        }

        if($('#sender_city').val() == '' || $('#sender_city').val() == undefined)
        {
            $('#city_error').empty().append("<p style='color: red;'>city is required</p>");

            return false;
        }
        // getUspsRates();
        
    })

    $('#usps_shipping_service').on('change',function(){

        resetErrorMessages();

        if($('#first_name').val() == '' || $('#first_name').val() == undefined)
        {
            $('#first_name_error').empty().append("<p style='color: red;'>first name is required</p>");

            return false;
        }

        if($('#last_name').val() == '' || $('#last_name').val() == undefined)
        {
            $('#last_name_error').empty().append("<p style='color: red;'>last name is required</p>");

            return false;
        }

        if($('#sender_state').val() == '' || $('#sender_state').val() == undefined)
        {
            $('#state_error').empty().append("<p style='color: red;'>state must be selected</p>");

            return false;
        }

        if($('#sender_address').val() == '' || $('#sender_address').val() == undefined)
        {
            $('#address_error').empty().append("<p style='color: red;'>complete street address must be mention</p>");

            return false;
        }

        if($('#sender_city').val() == '' || $('#sender_city').val() == undefined)
        {
            $('#city_error').empty().append("<p style='color: red;'>city is required</p>");

            return false;
        }

        getUspsRates();
    })

    validate_us_address = function()
    {
        let country = 250;
        let address = $('#sender_address').val();
        let state = $('#sender_state option:selected').text();
        let city = $('#sender_city').val();

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
                    $('#sender_zipcode').val(response.zipcode);
                    $('#zipcode_response').empty().append("<p style='color: #198754;'>According to your given Addrees, your zip code should be: <span>"+response.zipcode+"</span></p>");
                }else {
                    $('#zipcode_response').empty().append("<p style='color: red;'><span>"+response.message+"</span></p>");
                }
                $('#loading').fadeOut();
            }).catch(function(error){
                $('#zipcode_response').empty().append("<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
                $('#loading').fadeOut();
            })
        }
    }

    function getUspsRates(){
        let state = $('#sender_state option:selected').attr('data-state-code');
        let address = $('#sender_address').val();
        let city = $('#sender_city').val();
        let zipcode = $('#sender_zipcode').val();
        const service = $('#usps_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        
        $('#loading').fadeIn();
        $.get('{{ route("api.usps_sender_rates") }}',{
                sender_state: state,
                sender_address: address,
                sender_city: city,
                sender_zipcode: zipcode,
                service: service,
                order_id: order_id,
                buy_usps_label: true,

            }).then(function(response){
                if(response.success == true){
                    $('#calculated_rates').empty().append("<h4 class='text-danger'>Total Charges :" +response.total_amount+ " USD </h4>");
                    $('#total_price').val(response.total_amount);
                    $('#submitBtn').prop('disabled', false);
                }
                $('#loading').fadeOut();

            }).catch(function(error){
                console.log(error);
                $('#loading').fadeOut();
        })
        
    }

    function resetErrorMessages(){
        $('#state_error').empty();
        $('#address_error').empty();
        $('#city_error').empty();
    }


</script>
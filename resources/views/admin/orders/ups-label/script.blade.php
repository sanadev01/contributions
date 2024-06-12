<script>
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;
    $('#pickup_date').attr('min',today);

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

    $('#pickup_type').change(function() {
        if(this.checked) {
            enablePickupForm();
            if (validatePickupInputs() == true) {
                getUpsRates();
            }

        }else{
            disablePickupForm();
            if (validateInputs() == true) {
                getUpsRates();
            }
        }
        
    });

    $('#ups_shipping_service').ready(function() {

        resetErrorMessages();

        if (validateInputs() == true) {
            getUpsRates();
        }
    })

    $('#ups_shipping_service').on('change',function(){

        resetErrorMessages();

        if (validateInputs() == true) {
            getUpsRates();
        }
        
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

    function getUpsRates(){
        let pickup = $('input[type=checkbox]').prop('checked');
        let first_name = $('#first_name').val();
        let state = $('#sender_state option:selected').attr('data-state-code');
        let address = $('#sender_address').val();
        let city = $('#sender_city').val();
        let zipcode = $('#sender_zipcode').val();
        const service = $('#ups_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        let pickup_date = $('#pickup_date').val();
        let earliest_pickup_time = $('#earliest_pickup_time').val();
        let latest_pickup_time = $('#latest_pickup_time').val();
        let pickup_location = $('#pickup_location').val();
        

        if(pickup == true)
        {
            if (pickup_date == '' || pickup_date == undefined) {
                $('#pickup_date_response').empty().append("<p style='color: red;'>select pickup date</p>");
                return false;
            }
            if (earliest_pickup_time == '' || earliest_pickup_time == undefined) {
                $('#earliest_pickup_response').empty().append("<p style='color: red;'>select earliest pickup time</p>");
                return false;
            }
            if (latest_pickup_time == '' || latest_pickup_time == undefined) {
                $('#latest_pickup_response').empty().append("<p style='color: red;'>select latest pickup time</p>");
                return false;
            }
            if (pickup_location == '' || pickup_location == undefined) {
                $('#pickup_location_response').empty().append("<p style='color: red;'>Enter preferred pickup location</p>");
                return false;
            }
            
        }

        if (service == undefined) {
            $('#calculated_rates').empty().append("<p style='color: red;'>Please select UPS shipping service</p>");
            return false;
        }
        
        $('#loading').fadeIn();
        $.get('{{ route("api.ups_sender_rates") }}',{
                first_name: first_name,
                sender_state: state,
                sender_address: address,
                sender_city: city,
                sender_zipcode: zipcode,
                service: service,
                order_id: order_id,
                buy_usps_label: true,
                pickup: pickup,
                pickup_date: pickup_date,
                earliest_pickup_time: earliest_pickup_time,
                latest_pickup_time: latest_pickup_time,
                pickup_location: pickup_location

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

    function enablePickupForm() {
        $('#pickup_form').removeClass('d-none');
        $('#pickup_form').addClass('d-block');

        $('#pickup_date').prop('required', true);
        $('#pickup_date').prop('disabled', false);

        $('#earliest_pickup_time').prop('required', true);
        $('#earliest_pickup_time').prop('disabled', false);

        $('#latest_pickup_time').prop('required', true);
        $('#latest_pickup_time').prop('disabled', false);

        $('#pickup_location').prop('required', true);
        $('#pickup_location').prop('disabled', false);
    }

    function disablePickupForm() {
        $('#pickup_form').addClass('d-none');
        $('#pickup_form').removeClass('d-block');

        $('#pickup_date').prop('required', false);
        $('#pickup_date').prop('disabled', true);

        $('#earliest_pickup_time').prop('required', false);
        $('#earliest_pickup_time').prop('disabled', true);

        $('#latest_pickup_time').prop('required', false);
        $('#latest_pickup_time').prop('disabled', true);

        $('#pickup_location').prop('required', false);
        $('#pickup_location').prop('disabled', true);

        // $('#pickup_date, #earliest_pickup_time, #latest_pickup_time, #pickup_location').val('');
    }

    function validateInputs() {
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

        return true;
    }

    function validatePickupInputs() {
        if (pickup_date == '' || pickup_date == undefined) {
            return false;
        }
        if (earliest_pickup_time == '' || earliest_pickup_time == undefined) {
            return false;
        }
        if (latest_pickup_time == '' || latest_pickup_time == undefined) {
            return false;
        }
        if (pickup_location == '' || pickup_location == undefined) {
            return false;
        }

        return true;
    }

</script>
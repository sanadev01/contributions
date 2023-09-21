<script>
    $('#origin_country option:contains("United States")').prop('selected',true);

    $('#sender_state').on('change', function() {
        window.validate_us_address();
    });

    $('#sender_city').on('change', function() {
        window.validate_us_address();
    });

    $('#sender_address').on('change', function() {
        window.validate_us_address();
    });

    validate_us_address = function()
    {
        let country = $('#destination_country').val();
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
            }).catch(function(error){
                $('#zipcode_response').empty().append("<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
            })
        }
    }
</script>
<script>
    $('#origin_country option:contains("United States")').prop('selected',true);

    $('#destination_state').on('change', function() {
        window.validate_us_address();
    });

    $('#destination_city').on('change', function() {
        window.validate_us_address();
    });

    $('#destination_address').on('change', function() {
        window.validate_us_address();
    });

    validate_us_address = function()
    {
        let country = $('#destination_country').val();
        let address = $('#destination_address').val();
        let state = $('#destination_state option:selected').text();
        let city = $('#destination_city').val();

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
                    $('#destination_zipcode').val(response.zipcode);
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
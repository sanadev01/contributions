<script>
    window.addEventListener('address-searched', event => {
        if (event.detail.data) {
            $('#recipient_first_name').val(event.detail.data.first_name);
            $('#recipient_last_name').val(event.detail.data.last_name);
            $('#recipient_city').val(event.detail.data.city);
            $('#recipient_address').val(event.detail.data.address);
            $('#recipient_zipcode').val(event.detail.data.zip_code);

            if (event.detail.data.typeInternational === true) {
                $('#destination_country').val(event.detail.data.country_id);
                $('#recipient_state').val(event.detail.data.state_code);

                $('#destination_country').selectpicker('refresh');
                $('#recipient_state').selectpicker('refresh');
            }

            if (event.detail.data.typeInternational === false) {
                $('#us_destination_country').val(event.detail.data.country_id);
                $('#us_recipient_state').val(event.detail.data.state_code);

                $('#us_destination_country').selectpicker('refresh');
                $('#us_recipient_state').selectpicker('refresh');
            }
        }
        console.log(event.detail.data);
    })
</script>
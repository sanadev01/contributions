<script>
    $(document).ready(function () {
        var userId = $('#user_id').val();
        let isRequested = false;

        let countryId = null;
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#recipient_phone').keyup(function () {
            var phone = $(this).val();
            
            if (phone.length >= 4 && isRequested === false) {

                let checkBoxName = $('input[type="checkbox"]:checked').attr('name');
                
                if (checkBoxName == 'to_international') {
                    countryId = null
                }else{
                    countryId = 250;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('search.user-address') }}",
                    data: {
                        phone: phone,
                        country_id: countryId,
                        user_id: userId
                    },
                    success: function(data){
                        isRequested = true;
                        if (data.success == true) {
                            $('#address_list').removeClass('d-none');

                            $.each(data.addresses, function (key, address) {
                                console.log(address);
                            })
                        }
                    },
                    error: function(data){
                        isRequested = false;
                        console.log(data);
                    }
                })
            }
        });
    });
</script>
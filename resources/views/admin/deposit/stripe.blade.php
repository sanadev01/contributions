<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script>
    let paymentGateway = $('#payment_gateway').val();
    
    $('#payment_gateway').on('change', function(){
        paymentGateway = $(this).val();

        if(paymentGateway == 'stripe_ach'){
            $('#div_expiry_date').removeClass('d-block');
            $('#div_expiry_date').addClass('d-none');

            $('#div_security_code').removeClass('d-block');
            $('#div_security_code').addClass('d-none');

            $('#div_card_number').removeClass('d-block');
            $('#div_card_number').addClass('d-none');

            $('#div_routing_number').removeClass('d-none');
            $('#div_routing_number').addClass('d-block');

            $('#div_account_number').removeClass('d-none');
            $('#div_account_number').addClass('d-block');

            $('#accountNumber').attr('required', true);
            $('#routing_number').attr('required', true);

            $('#cardnumber').attr('required', false); 
            $('#expirationdate').attr('required', false); 
            $('#securitycode').attr('required', false);

            $('#country').on('change', function(){
                let countryId = $(this).val();
                if (countryId != '250' && countryId != undefined) 
                {
                    $('#stripe_error').css('display', 'block');
                    $('#stripe_error').html('<h4 class="text-danger">Stripe ACH payment is available only for United States Banks</h4>');
                }
            }); 

        } else {
            $('#div_routing_number').addClass('d-none');
            $('#div_routing_number').removeClass('d-block');

            $('#div_account_number').removeClass('d-block');
            $('#div_account_number').addClass('d-none');

            $('#div_expiry_date').addClass('d-block');
            $('#div_expiry_date').removeClass('d-none');

            $('#div_card_number').addClass('d-block');
            $('#div_card_number').removeClass('d-none');

            $('#div_routing_number').addClass('d-none');
            $('#div_routing_number').removeClass('d-block');

            $('#div_security_code').addClass('d-block');
            $('#div_security_code').removeClass('d-none');

            $('#accountNumber').attr('required', false);
            $('#routing_number').attr('required', false);

            $('#cardnumber').attr('required', true); 
            $('#expirationdate').attr('required', true); 
            $('#securitycode').attr('required', true);

            $('#stripe_error').css('display', 'block');
            $('#stripe_error').empty();
        }
        
    });

    $('.payment-form').on('submit', function (e) {
        var adminBalance = $("input[name='adminpay']:checked").val();
        let stripeKey = $(this).data('stripe-publishable-key');
        let countryId = $('#country').val();
        let paymentGateway = $('#payment_gateway').val();

        if(paymentGateway == 'stripe' && (adminBalance == 0 || adminBalance == undefined))
        {
            e.preventDefault();

            let stripeKey = $(this).data('stripe-publishable-key');
            let cardNumber = $('#cardnumber').val();
            let securitycode = $('#securitycode').val();
            let expDate = $('#expirationdate').val();
            let expMonth = expDate.split('/')[0];
            let expYear = expDate.split('/')[1];

            if(cardNumber != undefined || cardNumber != '' || securitycode != undefined || securitycode != '' || expDate != undefined || expDate != '') 
            {
                Stripe.setPublishableKey(stripeKey);
                Stripe.createToken({
                    number: cardNumber,
                    cvc: parseInt(securitycode),
                    exp_month: parseInt(expMonth),
                    exp_year: parseInt(expYear)
                }, stripeResponseHandler);
            } else {
                $('#stripe_error').html('<div class="alert alert-danger">Please enter card details</div>');
            }
        }

        if(paymentGateway == 'stripe_ach' && (adminBalance == 0 || adminBalance == undefined) && countryId == '250')
        {
            e.preventDefault();

            var stripe = Stripe(stripeKey);
            let accountNumber = $('#accountNumber').val();
            let routingNumber = $('#routing_number').val();
            let firstName = $('#first_name').val();
            let lastName = $('#last_name').val();
            let fullName = firstName.concat(lastName);

            stripe.createToken('bank_account', {
                    country: 'US',
                    currency: 'usd',
                    routing_number: routingNumber,
                    account_number: accountNumber,
                    account_holder_name: fullName,
                    account_holder_type: 'individual',
                })
                .then(function(result) {
                    if(result.error != undefined)
                    {
                        $('#stripe_error').css('display', 'block');
                        $('#stripe_error').empty().append("<h4 style='color: red;'>"+result.error.message+"</h4>");
                        toastr.error(result.error.message);
                    }

                    if(result.token != undefined)
                    {
                        $('#stripe_token').val(result.token.id);
                        $('.payment-form').get(0).submit();
                    }
            });
        }

        if(paymentGateway == 'stripe_ach' && (adminBalance == 0 || adminBalance == undefined) && countryId != '250')
        {
            e.preventDefault();
            $('#stripe_error').css('display', 'block');
            $('#stripe_error').html('<h4 class="text-danger">Stripe ACH payment is available only for United States Banks</h4>');
        }
        
    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
           console.log(response.error.message);
            $('#stripe_error').css('display', 'block');
            $('#stripe_error').empty().append("<h4 style='color: red;'>"+response.error.message+"</h4>");
            toastr.error(response.error.message)
        } else {
            $('#stripe_token').val(response.id);
            $('.payment-form').get(0).submit();
        }
    }
</script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
    let paymentGateway = $('#payment_gateway').val();
    
    $('#payment_gateway').on('change', function(){
        paymentGateway = $(this).val();
    });

    $('.payment-form').on('submit', function (e) {
        var adminBalance = $("input[name='adminpay']:checked").val();
        let stripe = $(this).data('stripe-payment');
        let stripeKey = $(this).data('stripe-publishable-key');
        
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
        
    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
           console.log(response.error.message);
            $('#stripe_error').css('display', 'block');
            $('#stripe_error').empty().append("<h4 style='color: red;'>"+response.error.message+"</h4>");
            toastr.error(response.error.message)
        } else {
            console.log(response.id);
            $('#stripe_token').val(response.id);
            $('.payment-form').get(0).submit();
        }
    }
</script>
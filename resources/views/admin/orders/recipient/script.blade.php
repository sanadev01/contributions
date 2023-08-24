<script>
    const Countries = @json($countryConstants);
    const Brazil = Countries.Brazil;
    const Chile = Countries.Chile;
    const Colombia = Countries.Colombia;
    const UnitedStates = Countries.US;
    const Netherlands = Countries.Netherlands;

    const CourierExpress = 'courier_express';
    const PostalService = 'postal_service';

    var selectedService;

    $(document).ready(function(){

        $('#address_id').on('change',function(){
            if ( $(this).val() == undefined || $(this).val() == "" ) return;
            $('#loading').fadeIn();
            $.post('{{ route("api.orders.recipient.update") }}',{
                address_id: $(this).val(),
                order_id: {{ $order->id }}
            })
            .then(function(response){
                if ( response.success ){
                    window.location.reload();
                }else{
                    $('#loading').fadeOut();
                    toastr.error(response.message)
                }

            }).catch(function(error){
                $('#loading').fadeOut();
            })
        });

        $('#accountType').on('change', function(){
            let val = $(this).val();
            if(val == 'individual'){
                $('#cpf_label_id').css('display', 'block')
                $('#cnpj_label_id').css('display', 'none')
                $('#tax_id').attr('placeholder', 'CPF')
            }else{
                $('#cpf_label_id').css('display', 'none')
                $('#cnpj_label_id').css('display', 'block')
                $('#tax_id').attr('placeholder', 'CNPJ')
            }
        })

        let serviceType = $('input[name="service"]:checked').val();
        if(serviceType !== '' || serviceType !== undefined)
        {
            this.selectedService = serviceType;
        }else{
            this.selectedService = PostalService;
        }

        if (this.selectedService == CourierExpress) {
            activeChileFields(serviceType);
        }

        if (this.selectedService == PostalService) {
            console.log('postal service need to toggle');
        }

        $('#country').ready(function() {
            let country = $('#country').val();
            let oldRegion = $('#region').data('value');
            let oldCommune = $('#commune').data('commune');
            if (country == Chile) {
                let serviceType = $('input[name="service"]:checked').val();
                if(serviceType !== '' || serviceType !== undefined)
                {
                    this.selectedService = serviceType;
                }else{
                    this.selectedService = PostalService;
                }

                activeChileFields(serviceType);
                return  ;
            }
            if(country == 94)
            {
                window.inactiveChileFields();
                window.activeGuatmalaFields();
                return;
            }

            if (country == Chile && this.selectedService == CourierExpress) {
                activeChileFields(this.selectedService);
                return getChileRegionsFromDB(oldRegion, oldCommune);
            }

            if (country == Chile && this.selectedService == PostalService) {
                activeChileFields(this.selectedService);
                return getRegionsFromCorreiosChile(oldRegion);
            }

            if (country != Chile) {
                inactiveChileFields(serviceType);

                if (country == UnitedStates) {
                    inactiveColombiaFields();
                    activateUSFields();
                }

                if (country == Brazil) {
                    inactiveUSFields();
                    activateBrazilFields();
                }

                if (country == Netherlands) {
                    $('#div_street_number').css('display', 'none');
                    $('#address2').css('display', 'none');
                    $('#cpf').addClass('d-none');
                    $("[name='state_id']").prop('required',true);
                }

                if (country == Colombia) {
                    activeColombiaFields();
                    return getStatesFromDB();
                    //return getColombiaRegionsFromDB();
                }

                // if (country == Colombia) {
                //     console.log('colombia');
                //     inactiveUSFields();
                //     activeColombiaFields();
                //     return getColombiaRegionsFromDB(oldRegion);
                // }
            }

        })

        $('input:radio[name="service"]').change(function(){
            this.selectedService = $(this).val();

            if (this.selectedService == CourierExpress) {
                activeChileFields(this.selectedService);
            }
        });

        $('#zipcode').on('change', function(){
            let country = $('#country').val();

            if (country == Brazil) {

                if ( $(this).val() == undefined || $(this).val() == '' ) return;
                $('#loading').fadeIn();

                validateBrazilianZipcode($(this).val());
            }

            if(val == '50'){
                window.activeColombiaFields();
                return getStatesFromDB();
                $('#loading').fadeIn();
            }else{
                window.inactiveColombiaFields();
            }
        });

        $('#country').on('change', function(){
            let country = $(this).val();
            let serviceType = $('input[name="service"]:checked').val();

            if (serviceType == undefined) {
                serviceType = PostalService;
            }
            if(country == 94)
            {

                window.inactiveChileFields();
                window.activeGuatmalaFields();
                return;
            }

            inactiveChileFields(serviceType);
            inactiveColombiaFields();
            inactiveUSFields();

            if (country == Chile && serviceType == CourierExpress) {
                activeChileFields(serviceType);
                return getChileRegionsFromDB();
            }

            if (country != Chile) {

                if (country == Brazil) {
                    activateBrazilFields();
                }

                if (country == UnitedStates) {
                    activateUSFields();
                }

                if (country == Netherlands) {
                    $('#div_street_number').css('display', 'none');
                    $('#address2').css('display', 'none');
                    $('#cpf').addClass('d-none');
                    $("[name='state_id']").prop('required',true);
                }

                if (country == Colombia) {
                    activeColombiaFields();
                    return getStatesFromDB();
                    //return getColombiaRegionsFromDB();
                }

                return getStatesFromDB();
                

                
            }

            if (country == Chile && serviceType == PostalService) {
                activeChileFields(serviceType);
                return getRegionsFromCorreiosChile();
            }
        });

        $('#region').on('change', function(){
            let country = $('#country').val();
            let serviceType = $('input[name="service"]:checked').val();
            let regionId = $(this).val();

            if (country == Chile && serviceType == PostalService) {
                console.log('chile region from correios chile');
                return getChileCommunesFromCorreios(regionId);
            }

            if (country == Chile && serviceType == CourierExpress) {
                return getChileCommunesFromDB(regionId);
            }

        });

        $('#address').on('change', function(){
            let address = $(this).val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let serviceType = $('input[name="service"]:checked').val();

            if (country == Chile && serviceType == PostalService && commune != undefined && commune != '' && address.length > 5) {
                return validateCorreiosChileAddress(commune, address);
            }

            if (country == UnitedStates  && serviceType == PostalService && address.length > 4) {
                let state = $('#state option:selected').text();
                let city = $('#city').val();

                if (state != undefined && city.length >= 4) {
                    return validateUnitedStatesAddress(state, city, address);
                }
            }
        });

        $('#street_no').on('change', function(){

            let address = $('#address').val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let serviceType = $('input[name="service"]:checked').val();

            if (country == Chile && serviceType == PostalService && commune != undefined && commune != '' && address.length > 5) {
                return validateCorreiosChileAddress(commune, address);
            }
        });

        $('#commune').on('change', function(){
            let address = $('#address').val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let serviceType = $('input[name="service"]:checked').val();

            if (country == Chile && serviceType == PostalService && commune != undefined && commune != '' && address.length > 5) {
                return validateCorreiosChileAddress(commune, address);
            }
        });

        $('#state').on('change', function() {
            let address = $('#address').val();
            let country = $('#country').val();
            let serviceType = $('input[name="service"]:checked').val();

            if (serviceType == undefined) {
                serviceType = PostalService;
            }

            if (country == UnitedStates  && serviceType == PostalService && address.length > 4) {
                console.log('here us ');
                let state = $('#state option:selected').text();
                let city = $('#city').val();

                if (state != undefined && city.length >= 4) {
                    return validateUnitedStatesAddress(state, city, address);
                }
            }
        });

        $('#city').on('change', function() {
            let address = $('#address').val();
            let country = $('#country').val();
            let serviceType = $('input[name="service"]:checked').val();

            if (serviceType == undefined) {
                serviceType = PostalService;
            }

            if (country == UnitedStates  && serviceType == PostalService && address.length > 4) {
                let state = $('#state option:selected').text();
                let city = $('#city').val();

                if (state != undefined && city.length >= 4) {
                    return validateUnitedStatesAddress(state, city, address);
                }
            }
        });

        $('#cocity').on('change', function(){
            let country = $('#country').val();

            if (country == Colombia) {

                if ( $(this).val() == undefined || $(this).val() == '' ) return;
                $('#loading').fadeIn();

                addColombiaZipcode($(this).val());
            }
        });
    });

    function activeChileFields(selectedService) {
        if (selectedService != CourierExpress) {
            $('#cpf').addClass('d-none');
        }

        $('#div_hd_state').addClass('d-none');
        $('#div_city').addClass('d-none');
        $('#div_street_number').addClass('d-none');

        $('#div_regions').removeClass('d-none');
        $('#div_communes').removeClass('d-none');
        $('#label_chile_address').removeClass('d-none');

        $('#label_address').addClass('d-none');

        $('#div_co_city').addClass('d-none');
        $('#div_co_dept').addClass('d-none');

        $('#state').prop('disabled', true);
        $('#city').attr('disabled', true);

        $('#region').prop('disabled', false);
        $('#commune').attr('disabled', false);
        $('#commune').prop('disabled', false);
    }

    function activeGuatmalaFields(){
        console.log('active guatmala fields'); 
        $('#cpf').addClass('d-none');
        $('#div_hd_state').css('display', 'none')
        $('#state_dev').css('display', 'none')
        $('#div_city').css('display', 'block')
        $('#div_street_number').css('display', 'block')
        $('#div_region').css('display', 'none')
        $('#div_communes').css('display', 'none') 
        $('#commune').prop('disabled', false);
        $('#label_address').css('display', 'inline-block')
        $('#state_div').css('display', 'none')
        $('#label_chile_address').css('display', 'none')

        $('#state').prop('disabled', true); 

        $('#region').prop('disabled', false); 
        $('#commune').attr('disabled', false);
    }

    function inactiveChileFields(selectedService) {

        if (selectedService != CourierExpress) {
            $('#cpf').removeClass('d-none');
        }

        $('#div_hd_state').removeClass('d-none');
        $('#div_city').removeClass('d-none');
        $('#div_street_number').removeClass('d-none');
        $('#label_address').removeClass('d-none');

        $('#div_regions').addClass('d-none');
        $('#div_communes').addClass('d-none');
        $('#label_chile_address').addClass('d-none');

        $('#state').prop('disabled', false);
        $('#city').attr('disabled', false);

        $('#region').prop('disabled', true);
        $('#commune').attr('disabled', true);
        $('#commune').prop('disabled', true);
    }

    function activateUSFields() {
        $('#div_street_number').removeClass('d-none');
        $('#cpf').removeClass('d-none');

        $('#div_street_number').addClass('d-none');
        $('#cpf').addClass('d-none');

        $('#div_hd_state').removeClass('d-none');
        $('#div_city').removeClass('d-none');
        $('#label_address').removeClass('d-none');

        $('#div_co_city').addClass('d-none');
        $('#div_co_dept').addClass('d-none');

        $('#div_regions').addClass('d-none');
        $('#div_communes').addClass('d-none');
        $('#label_chile_address').addClass('d-none');

        $('#state').prop('disabled', false);
        $('#city').attr('disabled', false);

        $('#region').prop('disabled', true);
        $('#commune').attr('disabled', true);
        $('#commune').prop('disabled', true);
    }

    function inactiveUSFields() {
        $('#div_street_number').removeClass('d-none');
        $('#cpf').removeClass('d-none');
    }

    function activateBrazilFields() {
        $('#div_street_number').removeClass('d-none');
        $('#cpf').removeClass('d-none');

        $('#div_hd_state').removeClass('d-none');
        $('#div_city').removeClass('d-none');
        $('#label_address').removeClass('d-none');

        $('#div_regions').addClass('d-none');
        $('#div_communes').addClass('d-none');
        $('#label_chile_address').addClass('d-');

        $('#div_co_city').addClass('d-none');
        $('#div_co_dept').addClass('d-none');

        $('#state').prop('disabled', false);
        $('#city').attr('disabled', false);

        $('#region').prop('disabled', true);
        $('#commune').attr('disabled', true);
        $('#commune').prop('disabled', true);
    }

    function activeColombiaFields() {
        $('#cpf').addClass('d-none');
        $('#state_div').addClass('d-none');
        $('#city_div').addClass('d-none');
        $('#div_street_number').addClass('d-none');
        $('#div_zipcode').addClass('d-block');
        $('#zipcode').prop('disabled', false);

        $('#div_co_city').removeClass('d-none');
        $('#div_co_dept').removeClass('d-none');

        //$('#div_regions').removeClass('d-none');
        $('#state').prop('disabled', true);
        $('#city').attr('disabled', true);

        $('#region').prop('disabled', false);
    }

    function inactiveColombiaFields() {
        $('#cpf').removeClass('d-none');
        $('#state_div').removeClass('d-none');
        $('#city_div').removeClass('d-none');
        $('#div_street_number').removeClass('d-none');
        $('#div_zipcode').removeClass('d-none');
        $('#zipcode').prop('disabled', false);

        $('#div_co_city').addClass('d-none');
        $('#div_co_dept').addClass('d-none');

        $('#div_regions').addClass('d-none');
        $('#state').prop('disabled', false);
        $('#city').attr('disabled', false);

        $('#region').prop('disabled', true);
    }

    function validateBrazilianZipcode(zipcode) {
        $.get('{{ route("api.orders.recipient.zipcode") }}',{
                zipcode: zipcode,
        }).then(function(response){
            console.log(response.data);
            if ( response.success ){
                $('#loading').fadeOut();
                $('#zipcode_response').empty().append("<p><b>According to your zipcode, your address should be this</b></p><p><span style='color: red;'>Address: </span><span>"+response.data.street+"</span></p><p><span style='color: red;'>City: </span><span>"+response.data.city+"</span></p><p><span style='color: red;'>State: </span><span>"+response.data.uf+"</span></p>");
            }else{
                $('#loading').fadeOut();
                $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                toastr.error(response.message)
            }
        }).catch(function(error){
            $('#loading').fadeOut();
        })
    }

    function addColombiaZipcode(zipcode) {
        const old_dept = $('#codept').val();
        $.ajax({
            type: 'POST',
            url: "{{route('api.orders.recipient.colombiaZipcode')}}",
            data: {country_id:  $('#country').val(), city:  $('#cocity').val()},
            success: function (data){
                if(data){
                    $('#zipcode').val(data.zipCode);
                    $.each(data.department,function(index, value){
                        $("#codept").find('option').remove().end()
                        $("#codept").html("<option value=''>No Data</option>");
                        $("#codept").append('<option value="'+value+'">'+value+'</option>');
                        $("#codept").selectpicker('refresh');
                    });
                    if(old_dept != undefined || old_dept != '')
                    {
                            $('#codept').val(old_dept);
                            $('#codept').selectpicker('val', old_dept);
                    }
                    
                }                
                $('#loading').fadeOut();
            },
            error: function(e) {
                    console.log(e);
            }
        });
    }

    function validateCorreiosChileAddress(commune, address) {
        $('#loading').fadeIn();

        $.get('{{ route("api.correios-chile-normalize-address") }}',{
            coummne: commune,
            direction: address,
        }).then(function(response){
            $('#loading').fadeOut();
            if (response.success == true  && response.data.cpostal != 0) {
                $('#zipcode').val(response.data.cpostal);
                $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.cpostal);
                $('#loading').fadeOut();
            }else if(response.success == true && response.data.cpostal == 0){
                $('#zipcode_response').empty().append("<p style='color: red;'><b>According to your Coummune, your address or street is Invalid</b></p><p><span style='color: red;'>zipcode: </span><span>");
                $('#loading').fadeOut();
            }else{
                $('#loading').fadeOut();
                $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                toastr.error(response.message)
            }
        }).catch(function(error){
            $('#loading').fadeOut();
            console.log(error);
            toastr.error('server error')
        })
    }

    function validateUnitedStatesAddress(state, city, address) {
        $('#loading').fadeIn();

        $.get('{{ route("api.orders.recipient.us_address") }}',{
            address: address,
            state: state,
            city: city,
        }).then(function(response){
            $('#loading').fadeOut();

            if ( response.success == true && response.zipcode != 0){
                $('#loading').fadeOut();
                $('#zipcode').val(response.zipcode);
                $('#zipcode_response').empty().append("<p><b>According to your given Addrees, your zip code should be this</b></p><p><span style='color: red;'>Zipcode: </span><span>"+response.zipcode+"</span></p>");
            }else {
                $('#loading').fadeOut();
                $('#zipcode_response').empty().append("<p style='color: red;'><b>According to address validator,</b></p><p><span style='color: red;'></span><span>"+response.message+"</span></p>");
            }
        }).catch(function(error){
            console.log(error);
            $('#loading').fadeOut();
            $('#zipcode_response').empty().append("<p style='color: red;'><b>According to address validator, your address is Invalid</b></p>");
        })
    }

    function getStatesFromDB()
    {
        const old_state = $('#state').val();
        const old_city = $('#cocity').val();
        $.ajaxSetup({
                headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
        $.ajax({
            type: 'POST',
            url: "{{route('admin.ajax.state')}}",
            data: {country_id:  $('#country').val()},
            success: function (data){
                if(data.cities){
                    $("#cocity").html("<option value=''>No Data</option>");
                    $("#codept").html("<option value=''>No Data</option>");
                    $.each(data.cities,function(index,city){
                        $('#cocity').append('<option value="'+city+'">'+city+'</option>');
                    });
                    $("#cocity").selectpicker('refresh');
                    if(old_city != undefined || old_city != '')
                    {
                            $('#cocity').val(old_city);
                            $('#cocity').selectpicker('val', old_city);
                    }
                }else {

                    $("#state").html("<option value=''>No Data</option>")
                    $.each(data,function(index,state){
                            $("#state").append('<option value="'+state.id+'">'+state.code+'</option>');
                    });
                        $("#state").selectpicker('refresh');
                    if(old_state != undefined || old_state != '')
                    {
                            $('#state').val(old_state);
                            $('#state').selectpicker('val', old_state);
                    }
                }
            },
            error: function(e) {
                    console.log(e);
            }
        });
    }

    function getChileRegionsFromDB(oldRegion = null, oldCommune = null) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#loading').fadeIn();
        $.ajax({
            type: 'GET',
            url: "{{route('api.hd-regions', ['countryId' => 46])}}",
            success: function (response){
                $('#loading').fadeOut();
                $('#region').empty();

                $.each(response.data, function(index, region){
                    $('#region').append('<option value="'+region.id+'">'+region.name+'</option>');
                    $('#region').selectpicker('refresh');
                });

                if (oldRegion != null) {
                    $('#region').val(oldRegion);
                    $('#region').selectpicker('refresh');

                    getChileCommunesFromDB(oldRegion, oldCommune);
                }
            },
            error: function(e) {
                $('#loading').fadeOut();
                console.log(e);
            }
        });
    }

    function getChileCommunesFromDB(regionId, oldCommune = null) {
        $('#loading').fadeIn();
        $.get('{{ route("api.hd-chile-communes") }}',{
            region_id: regionId
        }).then(function(response) {
            $('#loading').fadeOut();
            if(response.success == true)
            {
                $('#commune').attr('disabled', false);
                $('#commune').attr('name', 'commune_id');
                $('#commune').empty();

                $.each(response.data,function(key, commune)
                {
                    $('#commune').append('<option value="'+commune.id+'">'+commune.name+'</option>');
                    $('#commune').selectpicker('refresh');
                });

                if (oldCommune != null) {
                    console.log('putting old communer');
                    $('#commune').val(oldCommune);
                    $('#commune').selectpicker('refresh');
                }

            }else{
                $('#loading').fadeOut();
                console.log(response.message);
                $('#communes_response').removeClass('d-none');
                $('#communes_response').empty().append("<p style='color: red;'>Something went wrong, please try again later.</p>");
                toastr.error(response.message);
            }
        })
    }

    function getRegionsFromCorreiosChile()
    {
        $('#loading').fadeIn();
        $.get('{{ route("api.correios-chile-regions") }}')
        .then(function(response){
            $('#loading').fadeOut();

            if(response.success == true)
            {
                $('#region').attr('disabled', false);
                $('#region').empty();

                $.each(response.data,function(key, value)
                {
                    $('#region').append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                    $('#region').selectpicker('refresh');
                });
            }else {
                $('#loading').fadeOut();
                $('#regions_response').removeClass('d-none');
                $('#regions_response').empty().append("<p style='color: red;'>Something went wrong, please try again later.</p>");
                toastr.error(response.message)

            }
        }).catch(function(error){
            $('#loading').fadeOut();
            $('#regions_response').removeClass('d-none');
            $('#regions_response').empty().append("<p style='color: red;'>Something went wrong, please try again later.</p>");
            toastr.error('server error')
        })
    }

    function getChileCommunesFromCorreios(regionCode) {
        $('#loading').fadeIn();
        $.get('{{ route("api.correios-chile-communes") }}',{
            region_code: regionCode,
        }).then(function(response) {
            $('#loading').fadeOut();

            if (response.success == true) {
                $('#commune').attr('disabled', false);
                $('#commune').attr('name', 'commune_id');
                $('#commune').empty();

                $.each(response.data,function(key, value)
                {
                    $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                    $('#commune').selectpicker('refresh');
                });
            }else{
                $('#loading').fadeOut();
                $('#communes_response').removeClass('d-none');
                $('#communes_response').empty().append("<p style='color: red;'>Something went wrong, please try again later.</p>");
                toastr.error(response.message);
            }
        }).cattch(function(error){
            $('#loading').fadeOut();
            $('#communes_response').removeClass('d-none');
            $('#regions_response').empty().append("<p style='color: red;'>Something went wrong, please try again later.</p>");
            toastr.error('server error')
        })
    }

    function getColombiaRegionsFromDB(oldRegion = null) {
        $.ajaxSetup({
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#loading').fadeIn();
        $.ajax({
            type: 'GET',
            url: "{{route('api.hd-regions', ['countryId' => 50])}}",
            success: function (response){
                $('#region').empty();

                $.each(response.data, function(index, region){
                    $('#region').append('<option value="'+region.id+'">'+region.name+'</option>');
                    $('#region').selectpicker('refresh');
                });

                if (oldRegion != null) {
                    $('#region').val(oldRegion);
                    $('#region').selectpicker('refresh');
                }

                $('#loading').fadeOut();
            },
            error: function(e) {
                $('#loading').fadeOut();
                console.log(e);
            }
        });
    }
</script>

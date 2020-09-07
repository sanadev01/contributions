<script>
    const editInput = (id) => {
        input = document.getElementById(id)
        // input.value = ''
        input.readOnly=false
        input.required=true

        if(id == 'securitycode'){
            input.placeholder='***'
            input.name="cvv"
        }else{
            input.placeholder='0000 0000 0000 0000'
            input.name="card_no"
        }
    }
</script>
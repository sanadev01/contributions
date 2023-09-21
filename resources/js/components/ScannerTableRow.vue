<template>
    <tr :class="getBgClass">
        <td>
            {{ order.corrios_tracking_code }}
        </td>
        <td colspan="5" v-if="order.code != undefined && order.code != 200">
            {{ order.error }}
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.warehouse_number }}
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.weight }} Kg
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.weight_lbs }} Lbs 
            <hr>
            {{ order.weight_kg }} Kg
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.pobox }}
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.sender_name }}
        </td>
        <td v-if="order.code == undefined || order.code == 200">
            {{ order.customer_reference }}
        </td>
        <td v-if="canDelete">
            <button class="btn btn-danger" @click="$emit('remove-order',order,index)">
                Remove
            </button>
        </td>
    </tr>
</template>

<script>
export default {
    props: ['order','index','canDelete'],
    computed: {
        getBgClass(){
            return {
                'bg-light' : this.order.code ==200,
                'bg-warning' : this.order.code == 409,
                'bg-danger text-white' : this.order.code == 404
            }
        }
    }
}
</script>
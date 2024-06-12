<template>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Tracking Code</th>
                <th>WHR#</th>
                <th>Weight</th>
                <th>Volume Weight</th>
                <th>POBOX#</th>
                <th>Sender</th>
                <th>Customer Reference</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <scanner-table-row v-for="(order,index) in orders" :can-delete="editMode" :key="order.id" :order="order" :index="index" v-on:remove-order="removeOrder"/>
            <tr>
                <td colspan="8" class="h2 text-right px-5">
                    <span class="text-danger font-weight-bold">Weight :</span> {{ totalWeight }}
                    <span class="mx-3 text-danger font-weight-bold">Packages:</span> {{ totalPackages }}
                </td>
            </tr>
            <tr v-if="editMode">
                <td colspan="8">
                    <input type="text" class="w-100 text-center" style="height:50px;font-size:30px;" v-on:keyup.enter="addOrder" :disabled="hasError">
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import ScannerTableRow from '../components/ScannerTableRow';
export default {
    name: 'ScannerTable',
    props: {
        container : {
            type: Object,
            required: true
        },
        ordersCollection: {
            type: Array,
            required: true
        },
        editMode :{
            type:Boolean,
            default: true
        }
    },
    components: {
        ScannerTableRow
    },
    data() {
        return {
            orders: [],
            hasError: false,
        }
    },
    computed:{
        totalPackages(){
            let packageCount = 0;
            this.orders.forEach(order => {
                if ( order.code == null || order.code == 200 ){
                    packageCount++;
                }
            });

            return packageCount;
        },
        totalWeight(){
            let weightSum = 0;
            this.orders.forEach(order => {
                if ( order.code == null || order.code == 200 ){
                    weightSum += order.weight;
                }
            });

            return (weightSum).toFixed(2);
        }
    },
    methods: {
        getPackage(barCode){
            if ( barCode == undefined || barCode == '' ||  barCode.length <=0 ){
                return;
            }

            if (this.container.services_subclass_code.includes('SL'))
            {
                this.axios.post(`/sinerlog_container/${this.container.id}/packages/${barCode}`)
                .then((response) => {
                    if (response.data.order.code != 200) {
                        this.hasError = true;
                    }else{
                        this.hasError = false;
                    }
                    this.orders.push(response.data.order);
                })
                .catch(error=>{
                    console.log(error)
                })
            }
            else
            {
                this.axios.post(`/containers/${this.container.id}/packages/${barCode}`)
                .then((response) => {
                    if (response.data.order.code != 200) {
                        this.hasError = true;
                    }else{
                        this.hasError = false;
                    }
                    this.orders.push(response.data.order);
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        addOrder(event){
            this.getPackage(event.target.value);
            event.target.scrollIntoView();
            $(event.target).val('');
            $(event.target).focus();
        },
        removeOrder(order,index){

            if (this.hasError == true) {
                this.hasError = false;
            }
            if ( !order.id ){
                return this.orders.splice(index,1);
            }

            this.axios.delete(`/containers/${this.container.id}/packages/${order.id}`)
                .then(response=>{
                    this.orders.splice(index,1);
                })
                .catch(error=>{
                    // this.orders.splice(index,1);
                })
        }
    },
    mounted(){
        this.orders = this.ordersCollection;
    }
}
</script>
<template>
    <div>
        <div class="col-12 row mb-5" id="card-form">
            <div class="form-group col-4">
                <label>Select Order</label>
                <v-select :options="options" v-model="selectedOption"></v-select>
            </div>
            <div class="col-4">
                <label>Scan Products</label>
                <input type="text" class="form-control col-8" v-model="search">
            </div>
        </div>
        <div class="alert alert-danger" role="alert" v-if="error">
            {{ error }}
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Pobox Number</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(order, index) in filterOrder" :key="order.id">
                    <td>{{order.warehouse_number}}</td>
                    <td>{{order.user.name}}</td>
                    <td>{{order.user.pobox_number}}</td>
                    <td><span class="btn btn-sm btn-warning waves-effect waves-light">Inventory</span></td>
                    <td>
                        <ul>
                            <li v-for="product in order.products" :key="product.id">{{product.name}}</li>
                        </ul>
                    </td>
                    <td>
                        <button class="btn btn-primary" @click="placeOrder(order.id)">Place Order</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
    export default {
        props: {
            orders_prop: Array,
            base_url: String,
        },
        components: {
            vSelect
        },
        data() {
            return {
                orders: null,
                options: null,
                selectedOption: null,
                search: null,
            }
        },
        created(){
            this.setOrders();
            this.setOptions();
        },
        computed: {
            filterOrder(){
                if (this.orders && this.selectedOption && this.search) {
                    return this.orders.filter(order => {
                        return order.warehouse_number == this.selectedOption && order.products.filter(product => {
                            return product.sku.toLowerCase() == this.search.toLowerCase();
                        }).length > 0;
                    });
                }
            },
            error(){
                if(this.search && this.selectedOption && this.filterOrder.length == 0){
                    return 'No sales order found against this product';
                }
            }
        },
        methods: {
            setOrders(){
                this.orders = this.orders_prop;
            },
            setOptions() {
                this.options = this.orders.map(order => {
                    return order.warehouse_number;
                });
            },
            placeOrder(id){
                window.location.replace(this.base_url+'/parcels/' + id +'/edit');
            }
        }
    }
</script>
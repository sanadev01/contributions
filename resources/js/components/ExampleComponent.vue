<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <v-select :options="options"></v-select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select';
    export default {
        components: {
            vSelect
        },
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                options: [
                    'foo',
                    'bar',
                    'baz'
                ]
            }
        }
    }
</script>

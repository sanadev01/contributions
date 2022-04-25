<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Barcode Scanner</div>
                    <div class="card-body">
                        <!-- <ImageBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></ImageBarcodeReader> -->
                        <StreamBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></StreamBarcodeReader>
                        <div v-show="scanning" class="spinner-border text-warning" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="alert alert-success" role="alert" v-show="message">
                            {{message}}
                        </div>
                        <div class="alert alert-danger" role="alert" v-show="error">
                            {{error}}
                        </div>
                        <div class="row mt-2">
                            <div class="ml-auto mr-2">
                                <button @click="scanning = !scanning" type="button" class="btn btn-sm btn-primary">Toggle Scanner</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { ImageBarcodeReader } from "vue-barcode-reader";
import { StreamBarcodeReader } from "vue-barcode-reader";
export default {
    name: 'ScanLabel',
    components: {
        ImageBarcodeReader,
        StreamBarcodeReader
    },
    data() {
        return {
            form: {'tracking_code' : ''},
            error: null,
            scanning: false,
            message: ''
        };
    },
    created() {
        setInterval(() => {
            this.disableScanner();
        }, 45000)
    },
    mounted() {
        console.log('Component mounted.')
    },
    methods: {
        onDecode(decodedData) {
            this.message = '';
            this.error = '';

            this.form.tracking_code = decodedData;
            this.scanning = true;

            this.axios.post('/scan-label', this.form).then((response) => {
                if (response.status == 200 && response.data.success == true) {
                    this.message = response.data.message;
                }else{
                    this.error = response.data.message;
                }

                this.form.tracking_code = '';
                this.scanning = false;

            }).catch((error) => {

                this.form.tracking_code = '';
                this.error = error;
                this.scanning = false;
            })
        },
        onError(error) {
            this.error = error;
        },
        disableScanner(){
            if (this.form.tracking_code == '') {
                this.scanning = true;
            }
        }
    }
}
</script>
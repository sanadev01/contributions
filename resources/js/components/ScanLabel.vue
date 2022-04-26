<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Barcode Scanner</div>
                    <div class="card-body">
                        <!-- <ImageBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></ImageBarcodeReader> -->
                        <StreamBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></StreamBarcodeReader>
                        <div class="row align-items-center justify-content-center">
                            <div v-show="scanning" class="spinner-border text-warning" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="ml-auto mr-2">
                                <button @click="scanning = !scanning" type="button" class="btn btn-sm btn-primary">Scan</button>
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
        }, 30000)
    },
    mounted() {
        console.log('Component testing.')
    },
    methods: {
        onDecode(decodedData) {
            this.scanning = true;
            swal({
                title: "Scanning!",
                text: "scanning in process",
                icon: "info",
                buttons: false,
            });

            this.message = '';
            this.error = '';

            this.form.tracking_code = decodedData;
            this.axios.post('/scan-label', this.form).then((response) => {
                swal.close();
                if (response.status == 200 && response.data.success == true) {
                    swal({
                        title: "Success!",
                        text: response.data.message,
                        icon: "success",
                        buttons: false,
                        timer: 3000
                    });
                    this.message = response.data.message;
                    this.scanning = false;
                    this.form.tracking_code = '';
                    return;
                }else{
                    swal({
                        title: "Error!",
                        text: response.data.message,
                        icon: "error",
                        showConfirmButton: true,
                    }).then((value) => {
                        this.scanning = false;
                    });
                    this.error = response.data.message;
                }
                this.form.tracking_code = '';
            }).catch((error) => {

                swal({
                        title: "Error!",
                        text: error,
                        icon: "error",
                        showConfirmButton: true,
                }).then((value) => {
                    this.scanning = false;
                });
                this.form.tracking_code = '';
                this.error = error;
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
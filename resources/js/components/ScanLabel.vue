<template>
<div>
    <div class="row mb-2 col-4">
        <div class="col-md-2">
            <div class="form-check">
                <input class="form-check-input admin-api-settings" type="radio" name="scanning_source" id="camera" v-model="source" value="camera">
                <label class="form-check-label h5 ml-1" for="camera">
                    Camera
                </label>
            </div>
        </div>
        <div class="col-md-2 mt-1">
            <div class="form-check">
                <input class="form-check-input admin-api-settings" type="radio" name="scanning_source" id="scanner" v-model="source" value="scanner">
                <label class="form-check-label h5 ml-1" for="scanner">
                    Scanner
                </label>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Barcode Scanner</div>
                    <div class="card-body">
                        <div class="alert alert-danger" role="alert" v-if="error">
                            {{error}}
                        </div>
                        <div v-if="source == 'camera'">
                            <!-- <ImageBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></ImageBarcodeReader> -->
                            <StreamBarcodeReader @decode="onDecode" @error="onError" v-if="!scanning"></StreamBarcodeReader>
                            <div class="row align-items-center justify-content-center">
                                <h2 v-show="scanning" class="text-danger">Click scan button to open camera</h2>
                            </div>
                            <div class="row mt-2">
                                <div class="ml-auto mr-2">
                                    <button @click="scanning = !scanning" type="button" class="btn btn-sm btn-primary">Scan</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="source == 'scanner'">
                            <input type="text" class="w-100 text-center" style="height:50px;font-size:30px;" v-model="scannerInput" ref="search">
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
            source: '',
            form: {'tracking_code' : ''},
            scannerInput: '',
            error: null,
            scanning: false,
            message: ''
        };
    },
    created() {
        // setInterval(() => {
        //     this.disableScanner();
        // }, 60000)
    },
    mounted() {
        console.log('Component versioning.')
    },
    watch: {
        source(value) {
            this.scannerInput = '';
            this.form.tracking_code = '';

            if (value == 'scanner') {
                this.$nextTick(() => {
                    this.$refs.search.focus();
                });
            }
        },
        scannerInput(value) {
            if(value.length >= 13 && this.scanning == false) {
                this.form.tracking_code = value;
                this.addParcel();
            }
        }
    },
    methods: {
        onDecode(decodedData) {
            if (decodedData.length < 9) {
                return false;
            }

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
                        showConfirmButton: true,
                    }).then ((value) => {
                        setTimeout(() => { 
                            this.form.tracking_code = '';
                            this.scanning = false;
                        }, 1000);
                        
                        this.message = response.data.message;
                    });
                }else{
                    swal({
                        title: "Error!",
                        text: response.data.message,
                        icon: "error",
                        showConfirmButton: true,
                    }).then((value) => {
                        setTimeout(() => {
                            this.scanning = false;
                            this.form.tracking_code = '';
                        }, 1000);
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
                    setTimeout(() => {
                        this.scanning = false;
                        this.form.tracking_code = '';
                    }, 3000);
                });
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
        },
        addParcel(){
            swal({
                title: "Scanning!",
                text: "scanning in process",
                icon: "info",
                buttons: false,
            });
            this.message = '';
            this.error = '';
            this.scanning = true;

            this.axios.post('/scan-label', this.form).then((response) => {
                swal.close();
                if (response.status == 200 && response.data.success == true) {
                    swal({
                        title: "Scanning!",
                        text: response.data.message,
                        icon: "success",
                        buttons: false,
                        timer: 3000
                    });
                    this.message = response.data.message;
                    this.scanning = false;
                }else{
                    swal({
                        title: "Error!",
                        text: response.data.message,
                        icon: "error",
                        showConfirmButton: true,
                    }).then((value) => {
                         this.form.tracking_code = '';
                         this.scannerInput = '';
                         this.$refs.search.focus();
                         this.scanning = false;
                    });
                    this.error = response.data.message;
                }
                this.form.tracking_code = '';
                this.scannerInput = '';
            }).catch((error) => {
                swal({
                        title: "Error!",
                        text: error,
                        icon: "error",
                        showConfirmButton: true,
                    }).then((value) => {
                         this.form.tracking_code = '';
                         this.scannerInput = '';
                         this.$refs.search.focus();
                         this.scanning = false;
                });
                this.error = error;
            })
        },
    }
}
</script>
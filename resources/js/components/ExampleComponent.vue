<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <ImageBarcodeReader @decode="onDecode" @error="onError"></ImageBarcodeReader>
                        <StreamBarcodeReader @decode="onDecode" @error="onError"></StreamBarcodeReader>
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
        components: {
            ImageBarcodeReader,
            StreamBarcodeReader
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            onDecode(decodedData) {
                console.log(decodedData);
            },
            onError(error) {
                console.log(error);
            }
        }
    }
</script>

require('../bootstrap');

import swal from 'sweetalert';

import ScanLabel from '../components/ScanLabel.vue';

if (document.getElementById('vue-barcode')) {
    new Vue({
        el: '#vue-barcode',
        components: {
            ScanLabel
        }
    });
}
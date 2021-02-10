require('../bootstrap');

import ScannerTable from '../components/ScannerTable';

if (document.getElementById('vue-scanner')){
    new Vue({
        el: "#vue-scanner",
        components:{
            ScannerTable
        }
    })
}
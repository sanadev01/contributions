<!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/tether.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
<!-- END: Theme JS-->

<!-- END: Page JS-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>

<!-- boostrap js cdn start -->
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<!-- boostrap js cdn end -->

{{-- <script src="{{ mix('js/app.js') }}"></script> --}}
<script>
    $('.datepicker').pickadate({
        format: 'yyyy-m-d',
        max: 0
    });

   $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function print(div){
        var printArea = $("html").clone();
        if ( div ){
            printArea.find('body').html( $(div).html() )
            printArea.find('body').addClass('p-5');
        }

        var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
        WinPrint.document.write(printArea.html());
        WinPrint.document.close();
        setTimeout(function(){
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();
        },1000)
    }
</script>
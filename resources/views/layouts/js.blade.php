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
    function toggleDateSearch() {
        const div = document.getElementById('dateSearch');
        if (div.style.display != 'block') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';

        }

    }

    function toggleOrderPageSearch() {
        const div = document.getElementById('singleSearch');
        console.log(div);
        if (div.style.display != 'block') {
            div.style.display = 'block';
            // console.log('asdasd');
        } else {
            div.style.display = 'none';
            // console.log('aa');

        }
    }

    function toggleUserSearch() {
        const div = document.getElementById('userSearch');
        if (div.style.display != 'block') {
            div.style.display = 'block';
            // console.log('asdasd');
        } else {
            div.style.display = 'none';
            // console.log('aa');

        }
    }

    function toggleLogsSearch() {
        console.log('a');
        if ($('#logSearch').hasClass("hide")) {
            $('#logSearch').removeClass('hide');
        } else {
            $('#logSearch').addClass('hide');
        }


    }

    function handleChange(checkbox) {
        if (checkbox.checked == true) {
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            if ($(".bulk-orders:checked").length == 0) {
                document.getElementById("printBtnDiv").style.display = 'none';
            }
        }
    }

    function handleChangeSalesCommission(checkbox) {
        if (checkbox.checked == true) {
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            if ($(".bulk-sales:checked").length == 0) {
                document.getElementById("printBtnDiv").style.display = 'none';
            }
        }
    }

    function handleChangeContainer(checkbox) {
        if (checkbox.checked == true) {
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            if ($(".bulk-container:checked").length == 0) {
                document.getElementById("printBtnDiv").style.display = 'none';
            }
        }
    }
    var table = $('#example').DataTable({
        searching: false,
        paging: false,
        columnDefs: [{
                targets: [0, 1, 2, 3],
                visible: true
            },
            {
                targets: '_all',
                visible: false
            },
        ]
    });
    $(document).ready(function() {
        $('#visibilityToggle').change(function() {
            var item = $(this);
            // Get the column API object
            if (item.val() !== ' ' || item.val() !== null) {
                var column = table.column(item.val());
                // Toggle the visibility
                column.visible(!column.visible());
                $("#visibilityToggle").val("");
            }
        });


        $('a.toggle-vis').on('click', function(e) {
            e.preventDefault();

            // Get the column API object
            var column = table.column($(this).attr('data-column'));

            // Toggle the visibility
            column.visible(!column.visible());
        });
    });
    // script to adjust sidebar height as per screen resolution
    var body_height = $('body').css('height').replace('px', '');
    var logo_height = $(".main-menu .navbar-header").css('height').replace('px', '');
    var remaining_height = parseInt(body_height) - parseInt(logo_height);
    $('.navigation-main').css('height', remaining_height + "px");

    $('.datepicker').pickadate({
        format: 'yyyy-m-d',
        max: 0
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function print(div) {
        var printArea = $("html").clone();
        if (div) {
            printArea.find('body').html($(div).html())
            printArea.find('body').addClass('p-5');
        }

        var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
        WinPrint.document.write(printArea.html());
        WinPrint.document.close();
        setTimeout(function() {
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();
        }, 2000)
    }
</script>

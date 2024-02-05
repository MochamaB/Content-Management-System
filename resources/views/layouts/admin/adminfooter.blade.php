<!---- Page loader ----->
<script>
    document.onreadystatechange = function() {
        var loadingOverlay = document.getElementById("loading-overlay");
        if (document.readyState === "complete") {
            loadingOverlay.setAttribute("hidden", true);
        }
    };
</script>

<!---- Validation ----->
<script>
    $(document).ready(function() {
        $('.myForm').on("submit", function(event) {
            const $form = $(this);
            const $requiredFields = $form.find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').show();
                    $field.after('<div class="invalid-feedback">Please fill in this field.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').hide();
                }
            });
            if ($('.invalid-feedback:visible').length > 0) {
            isValid = false;
        }

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>
@if((count($routeParts) > 1) && ($routeParts[1] === 'edit'))

<script>
    $(document).ready(function() {
        // Elements
        const $editLink = $(".editLink");
        const $editFields = $(".form-control");
        const $editSelect = $(".formcontrol2");
        const $edittextarea = $(".textformcontrol");
        const $Display = $(".text-muted");
        const $nextBtn = $("#nextBtn");
        const $submitBtn = $(".submitBtn");

        // Hide edit fields and "Make Changes" button on page load
        $editFields.hide();
        $editSelect.hide();
        $edittextarea.hide();
        $nextBtn.hide();
        $submitBtn.hide();

        // "Edit" link click event
        // "Edit" link click event
        $editLink.on("click", function(event) {
            event.preventDefault();
            // Toggle edit fields and buttons visibility
            $editFields.toggle();
            $editSelect.toggle();
            $edittextarea.toggle();
            $Display.toggle();
            $nextBtn.toggle();
            $submitBtn.toggle();

            // Toggle "Edit" link text between "Edit" and "Cancel"
            $editLink.text(function(index, text) {
                return text === "Edit" ? "Cancel" : "Edit";
            });
        });

        // You can add logic for "Save" and "Cancel" buttons here if needed
        // For example, you can handle form submission to update the data in the database
    });
</script>
@endif
<!---- Fetch Units ----->
<script>
    $(document).ready(function() {
        $('#property_id').on('change', function() {
            var query = this.value;
            // Clear existing unit options before appending new ones
            $('#unit_id').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-units')}}",
                type: "POST",
                data: {
                    property_id: query,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {

                    // Loop through the properties of the data object
                    for (var unitId in data) {
                        if (data.hasOwnProperty(unitId)) {
                            // Access each unit ID and unit number
                            var unitNumber = data[unitId];
                            console.log('Unit ID: ' + unitId + ', Unit Number: ' + unitNumber);

                            // You can use these values as needed, for example, to populate a select element
                            // Here's an example of adding options to a select element with the id "unit_id"
                            $('#unit_id').append(new Option(unitNumber, unitId));
                        }
                    }

                }
            });

        });


    });
</script>
<!---- Fetch Last reading----->
<script>
    $(document).ready(function() {
        $('#unitcharge_id').on('change', function() {
            var inputValue = $(this).val();
            var propertyId = $('#property_id').val();
            var unitId = $('#unit_id').val(); // Get unit_id from input

            // alert(unitId);

            // Clear existing unit options before appending new ones
            $('#lastreading').empty();
            $('#startdate').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-meterReading')}}",
                type: "POST",
                data: {
                    unitcharge_id: inputValue,
                    property_id: propertyId, // Pass property_id to the server
                    unit_id: unitId,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var currentReadingValue = data.currentreading;
                    var endDateValue = data.enddate;
                    if (data && data.currentreading && data.enddate) {
                        //  alert(endDateValue);
                        $('#lastreading').val(currentReadingValue);
                        $('#startdate').val(endDateValue);
                    } else {
                        $('#lastreading').val("0.00");
                        $('#lastreading').prop('readonly', false);
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = String(today.getMonth() + 1).padStart(2, '0'); // Add leading zero if needed
                        var day = String(today.getDate()).padStart(2, '0'); // Add leading zero if needed
                        var formattedDate = year + '-' + month + '-' + day;
                        $('#startdate').val(formattedDate);
                    }

                }
            });

        });


    });
</script>


<!--- filter function -->
<script>
    
function applyFilters(filterField) {
    console.log('Filter Field:', filterField);
    var selectedValues = {}; // Object to store the selected values

    // Iterate over each filter input
    $('.formcontrol2').each(function() {
        var filterField = $(this).attr('id').replace('Filter', ''); // Extract the filter field name
        var selectedValue = $(this).val(); // Get the selected value



        selectedValues[filterField] = selectedValue; // Store the selected value in the object
    });

    // Show all table rows
    $('#dataTable tbody tr').show();

    // Iterate over each selected filter
    $.each(selectedValues, function(filterField, selectedValue) {
        if (selectedValue !== 'All') {
            // Special handling for date filter field
           
            // Iterate over each table row
            $('#dataTable tbody tr').each(function() {
                var row = $(this);
                var columnValue = row.find('td:contains(' + selectedValue + ')');
                    
                // Hide the row if it does not match the selected filter value
                if (columnValue.length === 0) {
                    row.hide();
                }
            });
        }
    });
    // Display the selected value in an alert
 

}

</script>

<!---- SHOW PDF OR TXT WHEN FILE IS UPLOADED----->

<!-- plugins:js -->
<script src="{{ asset('styles/admin/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ asset('styles/admin/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('styles/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('styles/admin/vendors/progressbar.js/progressbar.min.js') }}"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{ asset('styles/admin/js/off-canvas.js') }}"></script>
<script src="{{ asset('styles/admin/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('styles/admin/js/template.js') }}"></script>
<script src="{{ asset('styles/admin/js/settings.js') }}"></script>
<script src="{{ asset('styles/admin/js/todolist.js') }}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ asset('styles/admin/js/jquery.cookie.js')}}" type="text/javascript"></script>
<script src="{{ asset('styles/admin/js/dashboard.js') }}"></script>
<script src="{{ asset('styles/admin/js/Chart.roundedBarCharts.js') }}"></script>
<script src="{{ asset('styles/admin/js/myscript.js') }}"></script>
<!-- End custom js for this page-->
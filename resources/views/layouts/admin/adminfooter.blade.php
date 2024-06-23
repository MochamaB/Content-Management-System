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
                 //   $field.after('<div class="invalid-feedback">Please fill in this field.</div>');
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
@if((count($routeParts) > 1) && ($routeParts[1] === 'edit') || $routeParts[1] === 'show' || $routeParts[0] === 'system-setting' )

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

<!--- Script for formatting money/currency inputs -->
<script>
$(document).ready(function() {
  $('input.money, input[name="amount[]"], input[name="amount"]').each(function() {
    $(this)
      .attr('inputMode', 'decimal')
      .attr('placeholder', 'Enter Amount')
      .on('focus', function() {
        $(this).data('lastValue', $(this).val());
        $(this).attr('type', 'number');
      })
      .on('blur', function() {
        var value = $(this).val();
        $(this).attr('type', 'text');
        if (value !== '') {
          $(this).val((+value).toLocaleString());
        } else {
          $(this).val('');
        }
      });

    // If the input field already has a value when the page loads, format it
    var initialValue = $(this).val();
    if (initialValue !== '') {
      $(this).attr('type', 'text');
      $(this).val((+initialValue).toLocaleString());
    }
  });

  // Add this event listener to remove the commas
  $('form').on('submit', function() {
    $('input.money, input[name="amount[]"], input[name="amount"]').each(function() {
      var value = $(this).val();
      if (value !== '') {
        // Remove commas from the value
        $(this).val(value.replace(/,/g, ''));
      }
    });
  });
});

</script>


<!---- SHOW PDF OR TXT WHEN FILE IS UPLOADED----->

<!-- plugins:js -->
<script src="{{ url('styles/admin/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ url('styles/admin/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ url('styles/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ url('styles/admin/vendors/progressbar.js/progressbar.min.js') }}"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{ url('styles/admin/js/off-canvas.js') }}"></script>
<script src="{{ url('styles/admin/js/hoverable-collapse.js') }}"></script>
<script src="{{ url('styles/admin/js/template.js') }}"></script>
<script src="{{ url('styles/admin/js/settings.js') }}"></script>
<script src="{{ url('styles/admin/js/todolist.js') }}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ url('styles/admin/js/jquery.cookie.js')}}" type="text/javascript"></script>
<script src="{{ url('styles/admin/js/dashboard.js') }}"></script>
<script src="{{ url('styles/admin/js/Chart.roundedBarCharts.js') }}"></script>
<script src="{{ url('styles/admin/js/myscript.js') }}"></script>
<!-- End custom js for this page-->
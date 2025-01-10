

<script>
  (function($) {
    'use strict';
    $(document).ready(function() {
      // Start the progress bar as soon as the document is ready
      startProgressBar();

      var progressInterval;

      $(document).ajaxStart(function() {
        startProgressBar();
      });

      $(document).ajaxSend(function(event, xhr, options) {
        updateProgressBar();
      });

      $(document).ajaxStop(function() {
        stopProgressBar();
      });

      window.onbeforeunload = function() {
        startProgressBar();
      };

      $(window).on('load', function() {
        stopProgressBar();
      });

      function startProgressBar() {
        $("#progress-bar").css("width", "0%");
        $("#progress-container").show();
        updateProgressBar();
      }

      function updateProgressBar() {
        clearInterval(progressInterval);
        var percentage = 0;
        progressInterval = setInterval(function() {
          percentage += 1;
          $("#progress-bar").css("width", percentage + "%");

          if (percentage >= 100) {
            clearInterval(progressInterval);
          }
        }, 50);
      }

      function stopProgressBar() {
        clearInterval(progressInterval);
        $("#progress-bar").css("width", "100%");
        setTimeout(function() {
          $("#progress-container").hide();
          $("#progress-bar").css("width", "0%");
        }, 500);
      }
    });
  })(jQuery);
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
      }else {
        // Allow form to submit by returning true
        return true;
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
    const $inputGroups = $(".input-group");

    // Hide edit fields and "Make Changes" button on page load
    $editFields.hide();
    $editSelect.hide();
    $edittextarea.hide();
    $nextBtn.hide();
    $submitBtn.hide();
    $inputGroups.hide(); // Hide input groups

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
      $inputGroups.toggle();

      // Toggle "Edit" link text between "Edit" and "Cancel"
      $editLink.html(function(index, html) {
        return html.includes("mdi-lead-pencil") 
          ? '&nbsp;&nbsp;<i class="mdi mdi-close-circle mdi-16px text-danger" style="font-size:18px"></i>'
          : '&nbsp;&nbsp;<i class="mdi mdi-lead-pencil mdi-16px text-primary" style="font-size:18px"></i>';
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

<!--- Script for filtering by date -->
<script type="text/javascript">
 $(document).ready(function() {
   // Debug check
   if (!$('#daterange').length) {
        console.error('Daterange input not found in DOM');
        return;
    }
     // Add console logs to check initial state
     console.log('Elements found:', {
        daterange: $('#daterange').length,
        fromDate: $('#from_date').length,
        toDate: $('#to_date').length
    });
    var fromDate = "{{ request('from_date', null) }}";
    var toDate = "{{ request('to_date', null) }}";
    
    var ranges = {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
    
    // Function to find range label
    function findRangeLabel(start, end) {
        for (let label in ranges) {
            if (ranges[label][0].format('YYYY-MM-DD') === start.format('YYYY-MM-DD') &&
                ranges[label][1].format('YYYY-MM-DD') === end.format('YYYY-MM-DD')) {
                return label;
            }
        }
        return start.format('YYYY MMM') + ' - ' + end.format('YYYY MMM');
    }
    
    // Set initial value
    if (fromDate && toDate) {
        $('#daterange').val(findRangeLabel(moment(fromDate), moment(toDate)));
    } else {
        $('#daterange').val('This Month');
    }

    $('#daterange').daterangepicker({
        startDate: fromDate ? moment(fromDate) : moment().startOf('month'),
        endDate: toDate ? moment(toDate) : moment().endOf('month'),
        ranges: ranges,
        locale: {
            format: 'YYYY MMM',
            separator: ' - '
        },
        autoUpdateInput: false,
        showDropdowns: true,
        alwaysShowCalendars: false,
        showCustomRangeLabel: true,
        opens: 'left'
    }, function(start, end, label) {
        $('#from_date').val(start.format('YYYY-MM-DD'));
        $('#to_date').val(end.format('YYYY-MM-DD'));
        
        // Update input with range label or custom date range
        $('#daterange').val(label || (start.format('YYYY MMM') + ' - ' + end.format('YYYY MMM')));
        
        $('#daterange').prop('disabled', true);
        $('#dateRangeForm').submit();
    });

    // Rest of the code remains the same
    $('#property').on('change', function() {
        $('#dateRangeForm').submit();
    });

    $('#dateRangeForm').on('submit', function() {
        $('#daterange').prop('disabled', false);
    });

    $('#from_date').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#to_date').val(moment().endOf('month').format('YYYY-MM-DD'));
});
</script>

<!---- SHOW PDF OR TXT WHEN FILE IS UPLOADED----->

 

<script src="{{ asset('styles/client/assets/js/modernizr-2.6.2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js') }}"></script>
<script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/libs/jsPDF/jspdf.umd.min.js"></script>
<script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js') }}"></script>

<!-- plugins:js -->
<script src="{{ asset('styles/admin/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ asset('styles/admin/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('styles/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('styles/admin/vendors/progressbar.js/progressbar.min.js') }}"></script>
<!-- Dropzone JS -->
<script src="{{ asset('styles/admin/vendors/dropzone/dropzone.js') }}"></script>

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
<script src="{{ asset('styles/admin/js/select2.min.js') }}"></script>

<!-- End custom js for this page-->
  
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  
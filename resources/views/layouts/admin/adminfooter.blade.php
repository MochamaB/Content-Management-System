
<script>
    $(document).ready(function() {
        const $myForm = $("#myForm");
        const $requiredFields = $myForm.find('[required]');

        const validateForm = () => {
            let isValid = true;
            $requiredFields.each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').show();
                    $field.after('<div class="invalid-feedback"> <i class="menu-icon mdi mdi mdi-alert-circle"></i> Please fill in this field.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').hide();
                }
            });

            return isValid;
        };

        $myForm.on("submit", function(event) {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>
@if(($routeParts[1] === 'edit'))
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
  <!-- plugins:js -->
  <script src="{{ asset('resources/styles/admin/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{ asset('resources/styles/admin/vendors/chart.js/Chart.min.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/vendors/progressbar.js/progressbar.min.js') }}"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('resources/styles/admin/js/off-canvas.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/template.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/settings.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/todolist.js') }}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ asset('resources/styles/admin/js/jquery.cookie.js')}}" type="text/javascript"></script>
  <script src="{{ asset('resources/styles/admin/js/dashboard.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/Chart.roundedBarCharts.js') }}"></script>
  <script src="{{ asset('resources/styles/admin/js/myscript.js') }}"></script>
  <!-- End custom js for this page-->

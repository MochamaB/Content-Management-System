
<div class="modal fade" id="creditCheckModal" tabindex="-1" role="dialog" aria-labelledby="creditCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditCheckModalLabel">SMS Credit Check</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="creditCheckMessage">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="proceedWithoutSms">Proceed without SMS</button>
            </div>
        </div>
    </div>
</div>

<script>
  $(document).ready(function() {
    // Get the form using the myForm class since that's what we have in the HTML
    const $form = $('.myForm');
    console.log("Form element:", $form);
    
    // Handle form submission
    $form.on('submit', function(e) {
        e.preventDefault();
        console.log("Form submitted");
        
        // Store form reference
        const thisForm = this;
        console.log("thisForm:", thisForm);
        
        // Show loading state
        const submitButton = $(this).find('button[type="submit"]');
        console.log("Submit button:", submitButton);
        const originalButtonText = submitButton.text();
        submitButton.prop('disabled', true).text('Checking credits...');

         // Get form action path to identify the model
        const formAction = $(this).attr('action');
        const modelType = formAction.split('/').filter(Boolean).pop();
 
        // Create form data and append model type
        const formData = new FormData(this);
        formData.append('model_type', modelType);
        
        // Get the CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        console.log("CSRF Token:", token);
        
        // Perform credit check
        $.ajax({
            url: '/textmessage/check-credits',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function(response) {
                console.log("AJAX response:", response);
                if (response.hasCredits) {
                    // If we have credits, set SMS flag and submit form
                    $('#sendSms').val('1');
                    // Use native form submission
                   // Use native form submission
                   $(thisForm).get(0).submit();
                } else {
                    // Show modal if insufficient credits
                    $('#creditCheckMessage').text(response.message);
                    $('#creditCheckModal').modal('show');
                }
            },
            error: function(xhr) {
                console.error("AJAX error:", xhr);
                // Handle validation errors
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    // Display validation errors to user
                    console.log("Validation errors:", errors);
                    Object.keys(errors).forEach(field => {
                        const inputField = $(`#${field}`);
                        inputField.addClass('is-invalid');
                        inputField.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                } else {
                    console.error('Error:', xhr);
                  //  alert('An error occurred while processing your request. Please try again.');
                }
            },
            complete: function() {
                // Reset button state
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });

    // Handle "Proceed without SMS" button click
    $('#proceedWithoutSms').click(function() {
        $('#sendSms').val('0');
        $('#creditCheckModal').modal('hide');
        
        // Submit the form using native submit
        $('.myForm')[0].submit();
    });
    
    // Clear validation errors when user starts typing
    $form.find('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid')
            .siblings('.invalid-feedback').remove();
    });
});
</script>
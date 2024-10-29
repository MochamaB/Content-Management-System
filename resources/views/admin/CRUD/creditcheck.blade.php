<style>
    .modal-body .icon-close {
        font-size: 78px;
        /* Increase the size */
        color: red;
        /* Change color to red */
        display: block;
        /* Display as block for centering */
        margin: 0 auto;
        margin-bottom: 10px;
        /* Center horizontally */
    }

    /* Center content vertically in the modal body */
    .modal-body .preview {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        text-align: center;
    }

    .modal-body .message {
        word-wrap: normal;
        max-width: 90%;
        margin: 0 auto;
        font-size: 15px;
        white-space: normal;
    }
</style>
<div class="modal fade" id="creditCheckModal" tabindex="-1" role="dialog" aria-labelledby="creditCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:red !important;padding: 15px 46px;">
                <h5 class="modal-title" id="creditCheckModalLabel">Available Credit Check</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="preview">
                    <i class="icon-close"></i>
                    <h3>Insufficient Credits</h3>
                </div>
                <p class="message" id="creditCheckMessage"> </p>
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" id="proceedWithoutSms">Proceed without Text</button>
                <button type="button" class="btn btn-danger btn-lg text-white mb-0 me-0" data-dismiss="modal">Cancel and Topup</button>
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
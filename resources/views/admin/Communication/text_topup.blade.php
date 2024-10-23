<div class="" style="padding:30px;background-color:#fff;border: 1px solid #ccc;margin-top:-4px;">

    <!-- Success Message -->
    <div id="successMessage" class="alert alert-success" style="display: none;"></div>

    <!-- Error Message -->
    <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
    @php

    $user = Auth::user();
    @endphp
    <div class="separator">
        <hr>
        <span>TOP UP USING M-PESA EXPRESS</span>
        <hr>
    </div>
    <form method="POST" id="initiatepaymentForm" action="{{ route('mpesa.initiate') }}" class="myForm" novalidate>
        @csrf
        <ul class="ml-2 px-3 list-unstyled">
            <li>1. Confirm or <a href="" style="font-size: 15px; font-weight:600" class="editLink"> Edit</a> the phone number and the amount that you are toping up.
            </li>
            <div class="form-group">
                <label class="label"> Phone Number<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{$user->phonenumber ?? ''}}
                    </small>
                </h5>
                <input type="tel" class="form-control formhide" id="phonenumber" name="phonenumber" value="{{$user->phonenumber ?? ''}}" required>
            </div>
            <div class="form-group">
                <label class="label"> Top Up Amount<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $sitesettings->site_currency }} 300
                    </small>
                </h5>
                <div style="position: relative;">

                    <span class="currency" style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                    </span>
                    <input type="text" class="form-control formhide" name="amount" id="amount" value="300" style="text-align: left; padding-left: 45px;">
                </div>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Initiate Payment</button>
            </div><br />
            <li>2. You will receive a prompt on your phone with payment details.</li>
            <li>3. Enter your M-PESA PIN and click OK</li>
            <li>4. You will recieve a confirmation message from M-PESA</li>
        </ul>
        <p class="defaulttext">After you receive a successful reply from M-PESA, click the button at the bottom to check or complete transaction.
        </p>
    </form>

    <br />
    <div class="separator">
        <hr>
        <span>OR PAYBILL MENU</span>
        <hr>
    </div>
    <ul class="ml-2 px-3 list-unstyled">
        <li>1. Go to Mpesa Menu on your phone</li>
        <li>2. Select Paybill Option</li>
        <li>3. Enter the Business Number <b> 7999999</b></li>
        <li>4. Enter Account Number <b> {{ $sitesettings->company }}</b></li>
        <li>5. Enter the Amount <b>Type in the amount you wish to Top-Up</b></li>
        <li>6. Enter your MPESA PIN and send</li>
        <li>7. You will receive a confirmation from MPESA</li>
    </ul>

    <br />

    <hr>
    <form method="POST" id="checkpayment" action="{{ route('mpesa.checkStatus') }}" class="myForm" novalidate>
        @csrf

        <input type="hidden" id="transactionIdInput" name="transaction_id" value="">
        <input type="hidden" name="invoice_id" value="" required>
        <div class="col-md-6 mt-3">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Check / Complete Payment</button>
        </div>
    </form>

</div>
<script>
    $(document).ready(function() {
        // Elements
        const $editLink = $(".editLink");
        const $editFields = $(".formhide");
        const $Display = $(".text-muted");
        const $currency = $(".currency");


        // Hide edit fields and "Make Changes" button on page load
        $editFields.hide();
        $currency.hide();

        // "Edit" link click event
        // "Edit" link click event
        $editLink.on("click", function(event) {
            event.preventDefault();
            // Toggle edit fields and buttons visibility
            $editFields.toggle();
            $Display.toggle();
            $currency.toggle();
            // Toggle "Edit" link text between "Edit" and "Cancel"

        });

        // You can add logic for "Save" and "Cancel" buttons here if needed
        // For example, you can handle form submission to update the data in the database
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('initiatepaymentForm');
        const checkpayment = document.getElementById('checkpayment');
        const paymentForm = document.getElementById('paymentForm');
        const loadingOverlay = document.getElementById('loading-overlay');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const transactionIdInput = document.getElementById('transactionIdInput');
        const mpesaReceiptNumber = document.getElementById('mpesaReceiptNumber');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loader
            loadingOverlay.style.display = 'block';

            // Hide any existing messages
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            axios.post(this.action, new FormData(this))
                .then(function(response) {
                    loadingOverlay.style.display = 'none';
                    console.log(response); // Log the response for debugging
                    if (response.data.success) {
                        successMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Success! </strong>' + response.data.message;
                        successMessage.style.display = 'block';
                        transactionIdInput.value = response.data.transaction_id;


                        // You can start checking payment status here if needed
                        // checkPaymentStatus(response.data.transaction_id);
                    } else {
                        errorMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Error! </strong>' + response.data.message || 'An error occurred';
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(function(error) {
                    loadingOverlay.style.display = 'none';
                    console.error(error); // Log the error for debugging
                    errorMessage.textContent = 'Failed to initiate payment';
                    errorMessage.style.display = 'block';
                });
        });

        checkpayment.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!transactionIdInput.value) {
                errorMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Error! </strong> No payment has been initiated';
                errorMessage.style.display = 'block';
                return;
            }
            // Show loader
            loadingOverlay.style.display = 'block';

            // Hide any existing messages
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            axios.post(this.action, new FormData(this))
                .then(function(response) {
                    loadingOverlay.style.display = 'none';
                    if (response.data.success) {
                        successMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Success! </strong>' + response.data.message;
                        successMessage.style.display = 'block';
                        // Redirect to mpesareceipt with the payment ID
                        const paymentId = response.data.payment_id;
                        window.location.href = `/mpesareceipt/${paymentId}`;


                    } else {
                        errorMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Error! </strong>' + response.data.message || 'An error occurred';
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(function(error) {
                    loadingOverlay.style.display = 'none';
                    errorMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Error! </strong> Failed to complete payment';
                    errorMessage.style.display = 'block';
                });



        });

    });
</script>
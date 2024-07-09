@extends('layouts.admin.admin')

@section('content')

<style>
    ul,
    ol,
    dl {
        padding-left: 1rem;
        font-size: 0.89rem;
    }

    .separator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .separator hr {
        flex: 1;
        border: none;
        border-top: 1px solid #000;
        margin: 0 10px;
    }

    .separator span {
        font-weight: bold;
        font-size: 18px;
        color: #000;
    }
</style>

<div class="container">

    <div class="row">
        <div class="col-md-4">
            <img class="logo" src="{{ $sitesettings->getFirstMediaUrl('logo') ?: 'resources/uploads/images/noimage.jpg' }}" alt="Logo">

        </div>
        <div class="col-md-4">

            <ul style="list-style-type: none; display: table; margin: 0 auto; text-align: left;">
                <li><b>COMPANY:</b> {{$sitesettings->company_name }}</li>
                <li><b>LOCATION:</b> {{ $sitesettings->company_location}}</li>
                <li><b>EMAIL:</b> {{ $sitesettings->company_email }}</li>
                <li><b>TEL:</b> {{ $sitesettings->company_telephone }}</li>
            </ul>
        </div>
        <div class="col-md-4">
            <img class="logo" src="{{ asset('uploads/M-PESA.png') }}" style="height:100px;width:220px; margin-bottom:30px;">

        </div>
    </div>
    <hr><br />

    <div class="row">
        <!------- - ROW 1 -->
        <div class="col-md-6" style="border-right: 2px solid #dee2e6;padding-right:30px">
            <h4>INVOICE DETAILS</h4>
            <ul class="ml-2 px-3 list-unstyled">
                <li><b>REFERENCENO: </b> {{$invoice->referenceno}}</li>
                <li><b>NAME:</b> {{$invoice->name}} INVOICE</li>
                <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
                <li><b>INVOICE DATE:</b> {{\Carbon\Carbon::parse($invoice->created_at)->format('d M Y')}}</li>
                <li><b>DUE DATE:</b> {{\Carbon\Carbon::parse($invoice->duedate)->format('d M Y')}}</li>
                <li style="color:red; font-weight:700;font-size:20px"> TOTAL AMOUNT: {{ $sitesettings->site_currency }} @currency($invoice->totalamount)</li>
            </ul>
        </div>
        <!------- - ROW 2 -->
        <div class="col-md-6">

            <!-- Success Message -->
            <div id="successMessage" class="alert alert-success" style="display: none;"></div>

            <!-- Error Message -->
            <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
            @php
            $amountPaid = $invoice->payments->sum(function ($payment) {
            return $payment->paymentItems->sum('amount');
            });
            $amountdue = $invoice->totalamount - $amountPaid;

            @endphp
            <div class="separator">
                <hr>
                <span>PAY USING M-PESA EXPRESS</span>
                <hr>
            </div>
            <form method="POST" id="initiatepaymentForm" action="{{ route('mpesa.initiate') }}" class="myForm" novalidate>
                @csrf
                <ul class="ml-2 px-3 list-unstyled">
                    <li>1. Confirm or <a href="" style="font-size: 15px; font-weight:600" class="editLink"> Edit</a> the phone number and the amount that you are paying.
                    </li>
                    <div class="form-group">
                        <label class="label"> Phone Number<span class="requiredlabel">*</span></label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{$invoice->model->phonenumber ?? ''}}
                            </small>
                        </h5>
                        <input type="tel" class="form-control formhide" id="phonenumber" name="phonenumber" value="{{$invoice->model->phonenumber ?? ''}}" required>
                    </div>
                    <div class="form-group">
                        <label class="label"> Amount Due<span class="requiredlabel">*</span></label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $sitesettings->site_currency }}. {{$amountdue}}
                            </small>
                        </h5>
                        <div style="position: relative;">

                            <span class="currency" style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                            </span>
                            <input type="text" class="form-control formhide" name="amount" id="amount" value="{{$amountdue}}" style="text-align: left; padding-left: 45px;">
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="account_number" value="{{$invoice->referenceno ?? ''}}" required>
                    <input type="hidden" name="account_number" value="{{$invoice->referenceno ?? ''}}" required>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Initiate Payment</button>
                    </div><br />
                    <li>2. You will receive a prompt on your phone with payment details.</li>
                    <li>3. Enter your M-PESA PIN and click OK</li>
                    <li>4. You will recieve a confirmation message from M-PESA</li>
                </ul>
                <p style="font-size:15px">After you receive a successful reply from M-PESA, click the button at the bottom to check or complete transaction.
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
                <li>3. Enter the Business Number <b>{{ env('MPESA_BUSINESS_SHORTCODE');}}</b></li>
                <li>4. Enter Account Number <b>{{$invoice->referenceno}}</b></li>
                <li>5. Enter the Amount <b>{{$amountdue}}</b></li>
                <li>6. Enter your MPESA PIN and send</li>
                <li>7. You will receive a confirmation from MPESA</li>
            </ul>

            <br />

            <hr>
            <form method="POST" id="checkpayment" action="{{ route('mpesa.checkStatus') }}" class="myForm" novalidate>
                @csrf

                <input type="hidden" id="transactionIdInput" name="transaction_id" value="">
                <div class="col-md-6 mt-3">
                    <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Check / Complete Payment</button>
                </div>
            </form>

        </div>
    </div>
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
        const transactionIdInput2 = document.getElementById('transactionIdInput2');
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

                    if (response.data.success) {
                        successMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Success! </strong>' + response.data.message;
                        successMessage.style.display = 'block';
                        transactionIdInput.value = response.data.transaction_id;
                        transactionIdInput2.value = response.data.transaction_id;


                        // You can start checking payment status here if needed
                        // checkPaymentStatus(response.data.transaction_id);
                    } else {
                        errorMessage.innerHTML = '<i class="menu-icon mdi mdi-alert-circle mdi-24px"></i> <strong>Error! </strong>' + response.data.message || 'An error occurred';
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(function(error) {
                    loadingOverlay.style.display = 'none';
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




@endsection
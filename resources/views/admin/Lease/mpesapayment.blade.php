@extends('layouts.admin.admin')

@section('content')

<style>
    ul,
    ol,
    dl {
        padding-left: 1rem;
        font-size: 0.89rem;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <img class="logo" src="{{ $sitesettings->getFirstMediaUrl('logo') ?: 'resources/uploads/images/noimage.jpg' }}" alt="Logo">

        </div>
        <div class="col-md-4">
            <ul style="list-style-type: none; padding: 0; text-align: left;">
                <li><b>COMPANY:</b> {{$sitesettings->company_name }}</li>
                <li><b>LOCATION:</b> {{ $sitesettings->company_location}}</li>
                <li><b>EMAIL:</b> {{ $sitesettings->company_email }}</li>
                <li><b>TEL:</b> {{ $sitesettings->company_telephone }}</li>
            </ul>
        </div>
        <div class="col-md-4">
            <img class="logo" src="{{ asset('uploads/M-PESA.png') }}" style="height:100px;width:200px; margin-bottom:30px;">

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
            @php
            $amountPaid = $invoice->payments->sum(function ($payment) {
            return $payment->paymentItems->sum('amount');
            });
            $amountdue = $invoice->totalamount - $amountPaid;

            @endphp
            <h4>Pay Using M-Pesa Express</h4>
            <form method="POST" action="{{ route('mpesa.initiate') }}" class="myForm" novalidate>
                @csrf
                <ul class="ml-2 px-3 list-unstyled">
                    <li>1. Confirm or <a href="" class="editLink"> Edit</a> the phone number and the amount that you are paying.
                    </li>
                    <div class="form-group">
                        <label class="label"> Phone Number<span class="requiredlabel">*</span></label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{$invoice->model->phonenumber ?? ''}}
                            </small>
                        </h5>
                        <input type="tel" class="form-control" id="phonenumber" name="phonenumber" value="{{$invoice->model->phonenumber ?? ''}}" required>
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
                            <input type="text" class="form-control" name="amount" id="amount" value="{{$amountdue}}" style="text-align: left; padding-left: 45px;">
                        </div>
                    </div>
                    <input type="hidden" class="form-control" name="account_number" value="{{$invoice->referenceno ?? ''}}" required>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Initiate Payment</button>
                    </div><br />
                    <li>2. You will receive a prompt on your phone with payment details.</li>
                    <li>3. Enter your M-PESA PIN and click OK</li>
                    <li>4. You will recieve a confirmation message from M-PESA</li>

                </ul>
            </form>
            <hr>
            <br />
            <h4>Pay Using Paybill Menu</h4>
            <ul class="ml-2 px-3 list-unstyled">
                <li>1. Go to Mpesa Menu on your phone</li>
                <li>2. Select Paybill Option</li>
                <li>3. Enter the Business Number <b>{{ env('MPESA_BUSINESS_SHORTCODE');}}</b></li>
                <li>4. Enter Account Number <b>{{$invoice->referenceno}}</b></li>
                <li>5. Enter the Amount <b>{{$amountdue}}</b></li>
                <li>6. Enter your MPESA PIN and send</li>
                <li>7. You will receive a confirmation from MPESA</li>
            </ul>

        </div>
    </div>
</div>

<script>
  $(document).ready(function() {
    // Elements
    const $editLink = $(".editLink");
    const $editFields = $(".form-control");
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


@endsection
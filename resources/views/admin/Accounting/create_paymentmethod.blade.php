@extends('layouts.admin.admin')

@section('content')
<style>
    .hidden {
        display: none;
    }
</style>
<div class=" contwrapper">

    <h4>New Payment Method </h4>
    <hr>
    <form method="POST" action="{{ url('payment-method') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name <span class="requiredlabel">*</span></label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required>
                    <option value=""> -- Select Apartment ---</option>
                    @foreach($property as $item)
                    <option value="{{$item->id}}">{{$item->property_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Name <span class="requiredlabel">*</span></label>
                <select name="name" id="name" class="formcontrol2" placeholder="Select" required>
                    <option value="bank"> -- Select Value ---</option>
                    <option value="bank"> Bank</option>
                    <option value="cash"> Cash</option>
                    <option value="cheque"> Cheque</option>
                    <option value="m-pesa"> M-PESA</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Type <span class="requiredlabel">*</span></label>
                <select name="type" id="type" class="formcontrol2" placeholder="Select" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>

        <!-- Bank Details -->
        <div id="bank-details" class="hidden" style="background:#F4F5F7; padding:10px 30px;">
            <br />
            <div class="separator">
                <hr>
                <span>BANK PAYMENT CONFIGURATION</span>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Bank Name </label>
                        <input type="text" name="bank_name" class="formcontrol2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Branch Name </label>
                        <input type="text" name="branch_name" class="formcontrol2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Account Number </label>
                        <input type="text" name="account_number" class="formcontrol2">
                    </div>
                </div>
            </div>
        </div>

        <!-- M-PESA Details -->
        <div id="mpesa-details" class="hidden" style="background:#F4F5F7; padding:10px 30px;">
            <br />
            <div class="separator">
                <hr>
                <span>MPESA PAYMENT CONFIGURATION</span>
                <hr>
            </div>
            <div class="row">
            <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">M-PESA Shortcode </label>
                        <input type="text" name="mpesa_shortcode" class="formcontrol2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="label">Account Number</label>
                        <input type="text" id = "mpesa_account_number" name="mpesa_account_number" class="formcontrol2">
                    </div>
                </div>
               

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Consumer Key </label>
                        <input type="text" name="consumer_key" class="formcontrol2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Consumer Secret </label>
                        <input type="text" name="consumer_secret" class="formcontrol2">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Passkey </label>
                        <input type="text" name="passkey" class="formcontrol2">
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment Method</button>
        </div>

    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameSelect = document.getElementById('name');
        const typeSelect = document.getElementById('type');
        const bankDetails = document.getElementById('bank-details');
        const mpesaDetails = document.getElementById('mpesa-details');


        nameSelect.addEventListener('change', function() {
            const selectedName = nameSelect.value;

            // Reset type select and hide all details
            typeSelect.innerHTML = '';
            bankDetails.classList.add('hidden');
            mpesaDetails.classList.add('hidden');

            if (selectedName === 'bank') {
                typeSelect.innerHTML = `
                <option value="">-- Select Value --</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="direct_payment">Direct Payment</option>
            `;
               
            } else if (selectedName === 'm-pesa') {
                typeSelect.innerHTML = `
                <option value="">-- Select Value --</option>
                <option value="paybill">Paybill</option>
                <option value="till">Till Number</option>
            `;
               
            } else {
                // If not bank or m-pesa, hide type select and fill with the name value
                typeSelect.innerHTML = `<option value="${selectedName}">${selectedName}</option>`;
            }
        });

        typeSelect.addEventListener('change', function() {
            const selectedType = typeSelect.value;
            if (selectedType === 'paybill' || selectedType === 'till' ) {
                mpesaDetails.classList.remove('hidden');
            }else if (selectedType === 'bank_transfer' || selectedType === 'direct_payment') {
                
                bankDetails.classList.remove('hidden');
            }

        });
    });
</script>

@endsection
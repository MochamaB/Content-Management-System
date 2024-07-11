<!-- resources/views/payment_methods/edit.blade.php -->

@extends('layouts.admin.admin')

@section('content')
<style>
    .hidden {
        display: none;
    }
</style>
<div class=" contwrapper">

    <h4>Edit Payment Method
        <a href="" class="editLink">Edit</a>
    </h4>
    <hr>
    <form action="{{ url('payment-method/'. $paymentMethod->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="property_id" value="{{ $paymentMethod->property_id}}"/>
        <!-- Payment Name Select -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Name <span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $paymentMethod->name }}
                    </small>
                </h5>
                <select name="name" id="name" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="bank" {{ $paymentMethod->name == 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="cash" {{ $paymentMethod->name == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ $paymentMethod->name == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="m-pesa" {{ $paymentMethod->name == 'm-pesa' ? 'selected' : '' }}>M-PESA</option>
                    <option value="airtel-money" {{ $paymentMethod->name == 'airtel-money' ? 'selected' : '' }}>Airtel-Money</option>
                </select>
            </div>
        </div>

        <!-- Payment Type Select -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Type</label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $paymentMethod->type }}
                    </small>
                </h5>
                <select name="type" id="type" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="cash" {{ $paymentMethod->type == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ $paymentMethod->type == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="mpesa" {{ $paymentMethod->name == 'mpesa' ? 'selected' : '' }}>M-PESA</option>
                    <option value="airtel-money" {{ $paymentMethod->name == 'airtel-money' ? 'selected' : '' }}>Airtel-Money</option>
                </select>
            </div>
        </div>
        <!-- is_active Checkbox -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Active</label>
                <input type="checkbox" name="is_active" value="1" {{ $paymentMethod->is_active ? 'checked' : '' }}>
            </div>
        </div>

        <!-- Bank Details -->
        <div id="bank-details" class="{{ $paymentMethod->name == 'bank' ? '' : 'hidden' }}" style="background:#F4F5F7; padding:10px 30px;">
            <br />
            <div class="separator">
                <hr>
                <span>BANK PAYMENT CONFIGURATION</span>
                <hr>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Bank Name</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->bank_name }}
                            </small>
                        </h5>
                        <input type="text" name="bank_name" class="formcontrol2" value="{{ $paymentMethodConfig->bank_name ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Branch Name</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->branch_name }}
                            </small>
                        </h5>
                        <input type="text" name="branch_name" class="formcontrol2" value="{{ $paymentMethodConfig->branch_name ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Account Number</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->account_number }}
                            </small>
                        </h5>
                        <input type="text" name="account_number" class="formcontrol2" value="{{ $paymentMethodConfig->account_number ?? ''}}">
                    </div>
                </div>
            </div>
        </div>

        <!-- M-PESA Details -->
        <div id="mpesa-details" class="{{ $paymentMethod->name == 'm-pesa' ? '' : 'hidden' }}" style="background:#F4F5F7; padding:10px 30px;">
            <br />
            <div class="separator">
                <hr>
                <span>BANK PAYMENT CONFIGURATION</span>
                <hr>
            </div>
            <div class="row">
            <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">M-PESA Type</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->mpesa_type }}
                            </small>
                        </h5>
                        <select name="mpesa_type" class="formcontrol2">
                            <option value="paybill" {{ ($paymentMethodConfig->mpesa_type ?? '') == 'paybill' ? 'selected' : '' }}>Paybill</option>
                            <option value="till" {{ ($paymentMethodConfig->mpesa_type ?? '') == 'till' ? 'selected' : '' }}>Till Number</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">M-PESA Shortcode</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->mpesa_shortcode }}
                            </small>
                        </h5>
                        <input type="text" name="mpesa_shortcode" class="formcontrol2" value="{{ $paymentMethodConfig->mpesa_shortcode ?? '' }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Consumer Key</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->consumer_key }}
                            </small>
                        </h5>
                        <input type="text" name="consumer_key" class="formcontrol2" value="{{ $paymentMethodConfig->consumer_key ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Consumer Secret</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->consumer_secret }}
                            </small>
                        </h5>
                        <input type="text" name="consumer_secret" class="formcontrol2" value="{{ $paymentMethodConfig->consumer_secret ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Passkey</label>
                        <h5>
                            <small class="text-muted" style="text-transform: capitalize;">
                                {{ $paymentMethodConfig->passkey }}
                            </small>
                        </h5>
                        <input type="text" name="passkey" class="formcontrol2" value="{{ $paymentMethodConfig->passkey ?? '' }}">
                    </div>
                </div>
            </div>
        </div>


        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn">Edit Payment Method</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Your JavaScript logic to handle dynamic form changes
    document.addEventListener('DOMContentLoaded', function() {
        const nameSelect = document.getElementById('name');
        const typeSelect = document.getElementById('type');
        const bankDetails = document.getElementById('bank-details');
        const mpesaDetails = document.getElementById('mpesa-details');

        function toggleFields() {
            const name = nameSelect.value;

            bankDetails.classList.add('hidden');
            mpesaDetails.classList.add('hidden');

            if (name === 'bank') {
                bankDetails.classList.remove('hidden');
            } else if (name === 'm-pesa') {
                mpesaDetails.classList.remove('hidden');
            }
        }

        nameSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endpush
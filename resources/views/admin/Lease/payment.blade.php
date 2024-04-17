@extends('layouts.admin.admin')

@section('content')

@php

@endphp

<div class=" contwrapper">
    <h4>New Payment</h4>
    <hr>
    <form method="POST" action="{{ url('payment') }}" class="myForm" novalidate>
        @csrf
        <input type="hidden" name="instanceId" value="{{ $instance->id }}">
        <input type="hidden" name="model" value="{{$model}}">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$instance->property->id}}">{{$instance->property->property_name}}</option>
                </select>
            </div>
        </div>
        @if($instance->unit_id)
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$instance->unit->id}}">{{$instance->unit->unit_number}}</option>
                </select>
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <input type="hidden" class="form-control" name="model_type" id="model_type" value="{{$className}}">
            <input type="hidden" class="form-control" name="model_id" id="model_id" value="{{$instance->id}}">
            <input type="hidden" class="form-control" name="referenceno" id="referenceno" value="{{$referenceno}}">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Method<span class="requiredlabel">*</span></label>
                <select name="payment_method_id" id="payment_method_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Payment Method</option>
                    @foreach($PaymentMethod as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Code</label>
                <input type="text" class="form-control" name="payment_code" id="payment_code" value="">
            </div>
        </div>


        <!------- THIRD LEVEL INVOICE ITEMS -->
        <div class="col-md-8">
            <hr>
            <table class="table  table-bordered" style="font-size:12px;border:1px solid black;">
                <thead>
                    <tr class="tableheading" style="height:35px;">

                        <th>No.</th>
                        <th class="text-center">Charge </th>
                        <th class="text-center">Amount Due </th>
                        <th class="text-center">Amount Paid </th>

                        <th class="text-center">Amount To Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @if($model === 'Invoice')
                    @foreach($instance->invoiceItems as $key=> $item)
                    <tr style="height:35px;">
                        <td class="text-center" style="background-color:#dae3fa;">{{$key+1}}</td>
                        <td class="text-center" style="text-transform: capitalize;background-color:#dae3fa;">
                            {{$item->charge_name}} Charge
                        </td>
                        <td class="text-center" style="background-color:#dae3fa;">{{ $sitesettings->site_currency }}. @currency($item->amount) </td>
                        <td class="text-center" style="background-color:#dae3fa;">
                            @foreach ($instance->payments as $payment)
                            @foreach ($payment->paymentItems as $paymentItem)
                            @if ($paymentItem->unitcharge_id === $item->unitcharge_id)
                            {{ $sitesettings->site_currency }}.@currency($paymentItem->amount)</br>
                            @endif
                            @endforeach
                            @endforeach
                        </td>
                        <td class="text-center" style="padding:0px">
                            <div style="position: relative;">
                                @php
                                $amountdue = $item->amount - $instance->payments->sum(function ($payment) use ($item) {
                                return $payment->paymentItems->where('unitcharge_id', $item->unitcharge_id)->sum('amount'); })

                                @endphp
                                <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                                </span>
                                <input type="text" class="form-control" name="amount[]" id="amount" value="{{$amountdue}}" style="text-align: left; padding-left: 45px; border:none">
                            </div>

                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr style="height:35px;">
                        <td class="text-center" style="background-color:#dae3fa;">1</td>
                        <td class="text-center" style="text-transform: capitalize;background-color:#dae3fa;">
                            {{$instance->name}}
                        </td>
                        <td class="text-center" style="background-color:#dae3fa;">{{ $sitesettings->site_currency }}. @currency($instance->totalamount) </td>
                        <td class="text-center" style="background-color:#dae3fa;">
                            @if($instance->payments->isEmpty())
                            {{ $sitesettings->site_currency }}.@currency(0)</br>
                            @else
                            @foreach ($instance->payments as $payment)
                            {{ $sitesettings->site_currency }}.@currency($payment->totalamount)</br>
                            @endforeach
                            @endif
                        </td>
                        <td class="text-center" style="padding:0px">
                            <div style="position: relative;">
                                @php
                                $amountdue = $instance->totalamount - $instance->payments->sum('totalamount')
                                @endphp
                                <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                                </span>
                                <input type="text" class="form-control" name="totalamount" id="totalamount" value="{{$amountdue}}" style="text-align: left; padding-left: 45px; border:none">
                            </div>

                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div></br>


        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment</button>
        </div>

    </form>

</div>
<script>
    $(document).ready(function() {
        // Format the input value when the page loads
        formatInputValue();

        // Listen for the input event and format the value on change
        $('input[name="amount[]"]').on('input', function() {
            formatInputValue();
        });

        // Listen for the form submission and remove formatting before submitting
        $('form').on('submit', function(event) {
            // Remove formatting from input values
            $('input[name="amount[]"]').each(function() {
                $(this).val($(this).val().replace(/,/g, ''));
            });
        });

        function formatInputValue() {
            $('input[name="amount[]"]').each(function() {
                var value = parseFloat($(this).val().replace(/,/g, ''));
                if (!isNaN(value)) {
                    $(this).val(value.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }));
                }
            });
        }
    });
</script>
@endsection
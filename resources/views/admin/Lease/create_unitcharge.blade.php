@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Charge <span class="" style="font-style: italic;">(Bill/ Expense/ Utility/ Pre-Payment/ Fees)</span></h4>
    <hr>
    <form method="POST" action="{{ url('unitcharge') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Name of the Charge <span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="charge_name" name="charge_name" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Account <span class="requiredlabel">*</span></label>
                <select name="chartofaccounts_id" id="chartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$rentcharge->chartofaccounts_id ?? ''}}">{{$rentcharge->chartofaccounts->account_name ?? 'Select Account'}}</option>
                    @foreach($accounts as $accounttype => $account)
                    <optgroup label="{{ $accounttype }}">
                        @foreach($account as $item)
                        <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Charge Cycle<span class="requiredlabel">*</span></label>
                <select name="charge_cycle" id="charge_cycle" class="formcontrol2 charge_cycle" placeholder="Select" required>
                    <option value="">Select Option</option>
                    <option value="Once">Once</option>
                    <option value="Monthly"> Monthly</option>
                    <option value="Twomonths">Two Months</option>
                    <option value="Quaterly">Quaterly</option>
                    <option value="Halfyear">6 Months</option>
                    <option value="Year">1 Year</option>

                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Charge Type <span class="requiredlabel">*</span></label>
                <select name="charge_type" id="charge_type" class="formcontrol2" placeholder="Select" required>
                    <option value="fixed"> Fixed</option>
                    <option value="rate">Rate</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="rate" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Is it a recurring charge?<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="recurring_charge" name="recurring_charge" value="" required readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label"> Payment / Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="" required>
            </div>
        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Unit Charge</button>
        </div>

    </form>
</div>
<script>
    $(document).ready(function() {
        // Add change event listener to charge_cycle select element
        $("#charge_cycle").change(function() {
            // Get the selected value
            var selectedValue = $(this).val();
           // alert(selectedValue);

            // Update the recurring_charge select element based on the selected value
            if (selectedValue === "Once") {
                $("#recurring_charge").val("no");
                // Show and set the charge_type to "Fixed" and make it readonly
                $("#charge_type").val("fixed").attr("readonly","readonly");
            } else {
                $("#recurring_charge").val("yes");
                // Reset and enable the charge_type input
                $("#charge_type").val("Select Value").removeAttr("readonly");
            }
        });
    });
</script>
@endsection
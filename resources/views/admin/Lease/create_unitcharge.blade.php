@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Utility Charge on Unit</h4>
    <hr>
    <form method="POST" action="{{ url('unitcharge') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        <!-------- Unit Input---->
        @if($model === 'properties')
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Unit Number <span class="requiredlabel">*</span></label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    @foreach($unit as $item)
                    <option value="{{$item->id}}">{{$item->unit_number}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @else
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>
            </div>
        </div>
        @endif
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Origin of New Charge <span class="requiredlabel">*</span></label>
                <select id="charge_origin" class="formcontrol2" placeholder="Select" required>
                    <option value=""> Select Value</option>
                    <option value="utilities"> From Property Utilities</option>
                    <option value="newcharge">New Charge</option>
                </select>
            </div>
        </div>

        <div class="col-md-8" id="utilitySelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select Utility<span class="requiredlabel">*</span></label>
                <select id="utility" class="formcontrol2 " placeholder="Select">
                    <option value="">Select Utility</option>
                    @foreach($utilities as $utility)
                    <option value="{{$utility->utility_name}}">{{$utility->utility_name}}</option>
                    @endforeach

                </select>
            </div>
            <input type="hidden" class="form-control" name ="utility_id" id="utility_id" value="" required>
        </div>

        <div class="col-md-8" id="newChargeInput" style="display: none;">
            <div class="form-group">
                <label class="label">Name of New Charge <span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="newCharge" value="" required>
            </div>
        </div>
        <div class="col-md-8" id="account" style="display: none;">
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
        <div class="col-md-8" id="chargeCycle" style="display: none;">
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

        <div class="col-md-8" id="chargeType" style="display: none;">
            <div class="form-group">
                <label class="label">Charge Type <span class="requiredlabel">*</span></label>
                <select name="charge_type" id="charge_type" class="formcontrol2" placeholder="Select" required>
                    <option value="fixed"> Fixed</option>
                    <option value="units">Per Unit</option>
                </select>
            </div>
        </div>
        <div class="col-md-8" id="amount" style="display: none;">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id='rate' name="rate" value="" required>
            </div>
        </div>
        <div class="col-md-8" id="recurring" style="display: none;">
            <div class="form-group">
                <label class="label">Is it a recurring charge?<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="recurring_charge" name="recurring_charge" value="" required readonly>
            </div>
        </div>
        <div class="col-md-8" id="startdate" style="display: none;">
            <div class="form-group">
                <label class="label"> Payment / Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="" required>
            </div>
        </div>
        <hr>
        <div class="col-md-6" id="submit" style="display: none;">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Unit Charge</button>
        </div>

    </form>
</div>
<script>
    $(document).ready(function() {
        $('#charge_origin').on('change', function() {
            var query = this.value.trim();

            console.log(query);

            var $utilitySelect = $('#utilitySelect');
            var $utility = $('#utility');
            var $newChargeInput = $('#newChargeInput');
            var $newCharge = $('#newCharge');

            var $account = $('#account');
            var $chargeCycle = $('#chargeCycle')
            var $chargeType = $('#chargeType')
            var $amount = $('#amount')
            var $recurring = $('#recurring')
            var $startdate = $('#startdate')
            var $submit = $('#submit');

            // Remove the name attribute from both inputs initially
            $utility.removeAttr('name');
            $newCharge.removeAttr('name');

            if (query === "utilities") {
                $newChargeInput.hide();
                $utilitySelect.show();
                $utility.show().attr('name', 'charge_name');

                $account.show();
                $chargeCycle.show();
                $chargeType.show();
                $amount.show();
                $recurring.show();
                $startdate.show(); // Set the name attribute only for the visible select
                $submit.show();
            } else if (query === "newcharge") {
                $utilitySelect.hide();
                $newChargeInput.show();
                $newCharge.show().attr('name', 'charge_name'); // Set the name attribute only for the visible select

                $account.show();
                $chargeCycle.show();
                $chargeType.show();
                $amount.show();
                $recurring.show();
                $startdate.show(); // Set the name attribute only for the visible select
                $submit.show();
                $submit.show();
            } else {
                $userSelect.hide();
                $vendorSelect.hide();
                $submit.hide();
            }
        });
    });
</script>
<!---- Fetch Last reading----->
<script>
    $(document).ready(function() {
        $('#utility').on('change', function() {
            var inputValue = $(this).val();
            var propertyId = $('#property_id').val();
            // var unitId = $('#unit_id').val(); // Get unit_id from input

            // alert(inputValue);

            // Clear existing unit options before appending new ones
          
            $('#rate').empty();
            $('#recurring_charge').empty();
            $('#utility_id').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-charge')}}",
                type: "POST",
                data: {
                    charge_name: inputValue,
                    property_id: propertyId, // Pass property_id to the server
                    //   unit_id: unitId,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);

                    if (data) {
                        //  alert(endDateValue);
                        // Set chartofaccounts_id in the Account select input
                        $('#chartofaccounts_id').val(data.chartofaccounts_id);
                        // Set default_charge_cycle in the Charge Cycle select input
                        $('#charge_cycle').val(data.default_charge_cycle);

                        // Set utility_type in the Charge Type select input
                        $('#charge_type').val(data.utility_type);

                        // Set the rate value in the input field
                        $('#rate').val(data.default_rate);

                        $('#utility_id').val(data.id);

                        // Handle recurring charge (yes/no)
                        if (data.is_recurring_by_default === 1) {
                            $('#recurring_charge').val("yes");
                        } else {
                            $('#recurring_charge').val("no");
                        }

                    }

                }
            });

        });


    });
</script>

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
                $("#charge_type").val("fixed").attr("readonly", "readonly");
            } else {
                $("#recurring_charge").val("yes");
                // Reset and enable the charge_type input
                $("#charge_type").val("Select Value").removeAttr("readonly");
            }
        });
    });
</script>
@endsection
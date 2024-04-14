@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Expense </h4>
    <hr>
    <form method="POST" action="{{ url('expense') }}" class="myForm" novalidate>
        @csrf
        <div class="row">
            <!--- --------- ROW 1 ----->
            <div class="col-md-6" style="border-right: 2px solid #dee2e6;padding-right:30px">

                <div class="form-group">
                    <label class="label">Property Name</label>
                    <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                        <option value="{{$property->id}}">{{$property->property_name}}</option>
                    </select>
                </div>

                @if($model === 'units')
                <div class="form-group">
                    <label class="label">Unit Number</label>
                    <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                        <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                    </select>
                </div>
                @endif
                <div class="form-group">
                    <label class="label">Account <span class="requiredlabel">*</span></label>
                    <select name="chartofaccount_id" id="chartofaccount_id" class="formcontrol2" placeholder="Select" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $accounttype => $account)
                        <optgroup label="{{ $accounttype }}">
                            @foreach($account as $item)
                            <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">Name of the Expense<span class="requiredlabel">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="" required>
                </div>

                <input type="hidden" class="form-control" name="model_type" value="App\Models\Vendor" required>

                <div class="form-group">
                    <label class="label"> Pay To Vendor<span class="requiredlabel">*</span></label>
                    <select name="model_id" class="formcontrol2 " placeholder="Select" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="form-group">
                    <label class="label"> Description <span style="text-transform:capitalize"> (Enter brief description of expense)</span></label>
                    <textarea name="description" class="form-control" id="exampleTextarea1" rows="4" columns="6"></textarea>
                </div>

                <div class="form-group">
                    <label class="label">Amount<span class="requiredlabel">*</span></label>
                    <input type="number" class="form-control" name="totalamount" value="" required>
                </div>


                <input type="hidden" class="form-control" id="status" name="status" value="unpaid" required readonly>

                <div class="form-group">
                    <label class="label"> Due on Date<span class="requiredlabel">*</span></label>
                    <input type="date" class="form-control" id="duedate" name="duedate" value="" required>
                </div>
                <hr>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Expense</button>
                </div>
            </div>
            <!------- - ROW 2 -->
            <div class="col-md-6 d-none d-md-block" >

                <div class="form-group">
                    <label class="label">Upload Receipt / Attachment</label>
                    <input type="file" name="" value="" class="form-control" id="" />
                    <img class="img-fluid" id="logo-image-before-upload" src="{{ url('uploads/vectors/addfile.png') }}">

                </div>

            </div>
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
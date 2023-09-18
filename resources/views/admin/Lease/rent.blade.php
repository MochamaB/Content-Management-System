<h4><b> Add Rent Details</b></h4>
<hr>

<div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-information text-muted me-1"></i>
        <h5><a href="{{ url('skiprent') }}" class="nextBtn" id="nextBtn">Click Next</a> if there is no Rent Charge</a></h5>
       
    </div>
</div><br />
<form method="POST" action="{{ url('rent') }}" id="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="col-md-6">

        <input type="hidden" class="form-control" id="property_id" name="property_id" value="{{$lease->property_id ?? ''}}">
        <input type="hidden" class="form-control" id="unit_id" name="unit_id" value="{{$lease->unit_id ?? ''}}">
        <input type="hidden" class="form-control" id="parent_utility" name="parent_utility" value="0">
        <div class="form-group">
            <h4>Rent <span class="text-muted">(optional)</span></h4>
            <input type="hidden" class="form-control" id="charge_name" name="charge_name" value="rent" readonly>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="label">Rent Cycle<span class="requiredlabel">*</span></label>
            <select name="charge_cycle" id="charge_cycle" class="formcontrol2" placeholder="Select" required>
                <option value="{{$rentcharge->charge_cycle ?? ''}}">{{$rentcharge->charge_cycle ?? 'Select Rent Cycle'}}</option>
                <option value="Monthly"> Monthly</option>
                <option value="Quaterly">Quaterly</option>
                <option value="Halfyear">6 Months</option>
                <option value="Year">1 Year</option>

            </select>
        </div>
    </div>
    <!---------   ---->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Account<span class="requiredlabel">*</span></label>
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
                <input type="hidden" class="form-control" name="charge_type" value="fixed" required readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="rate" value="{{$lease->unit->rent ?? ''}}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Next Due Date<span class="requiredlabel">*</span></label>
                <input type="hidden" class="form-control" id="startdate" name="startdate" value="{{$rentcharge->startdate ?? $lease->startdate ?? ''}}" required>
                <input type="date" class="form-control" id="nextdate" name="nextdate" value="{{$rentcharge->nextdate ?? ''}}" required>
                <input type="hidden" class="form-control" id="recurring_charge" name="recurring_charge" value="yes">
            </div>
        </div>



    </div><br />
    <!--------- ----------- -->
    <div class="addfields" style="margin-bottom:30px">
        @if(!empty($splitRentcharges))
        @foreach ($splitRentcharges as $splitCharge)
        <div class="row dynamicadd">
            <div class="col-md-12">
                <button type="button" class="btn-danger text-white float-end cancel-field">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Charge Name<span class=""></span></label>
                    <input type="text" class="form-control" id="splitcharge_name[]" name="splitcharge_name[]" value="{{ $splitCharge->charge_name ?? '' }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Account<span class=""></span></label>
                    <select name="splitchartofaccounts_id" id="splitchartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$splitCharge['chartofaccounts_id'] ?? ''}}">{{$splitCharge->chartofaccounts->account_name ?? 'Select Account'}}</option>
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
                    <label class="label">Charge Type<span class="requiredlabel">*</span></label>
                    <select name="splitcharge_type" id="splitcharge_type" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$splitCharge['charge_type'] ?? ''}}">{{$splitCharge['charge_type'] ?? 'Select Account'}}</option>
                        <option value="fixed"> Fixed Amount</option>
                        <option value="units"> By Units</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label class="label ratelabel">Amount<span class="requiredlabel">*</span></label>
                    <input type="text" class="form-control" name="splitrate" value="{{$splitCharge['rate'] ?? ''}}" required>
                </div>
            </div>
        </div>
        @endforeach


        @endif
    </div>


    <div class="col-md-12" style="margin-bottom:30px">
        <h5><a class=" split_rent"><i class="menu-icon mdi mdi-plus-circle"></i> Split the Rent Charge </a>
            <span class="text-muted">(Will be included in Rent Invoices & Payments)</span>
        </h5>
    </div>








    <div class="col-md-8">
        <div class="row">
            <div class="col-md-4">
                <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous:Cosigners</button>
            </div>
            <div class="col-md-3">
            <a href="{{ url('skiprent') }}" class="btn btn-danger btn-lg text-white mb-0 me-0 previousBtn nextBtn" id="">Skip:Utilities</a>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 nextBtn" id="">Next:Deposit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Define the container where dynamic fields will be added
        var dynamicFieldsContainer = $('.addfields');

         // Function to handle the removal of dynamic fields
        function removeDynamicField() {
        $(this).closest('.dynamicadd').remove();
    }

        // Bind a click event to your button
        $('.split_rent').click(function(e) {
            e.preventDefault();

            // Create a new dynamic field div
            var dynamicFieldDiv = $('<div>', {
                class: ' row dynamicadd'
            });

            // Populate the dynamic field div with form elements
            dynamicFieldDiv.html(`
            <div class="col-md-12">
                        <button type="button" class="btn-danger text-white float-end cancel-field">
                          <span aria-hidden="true">&times;</span>
                        </button>
            </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Charge Name<span class="requiredlabel">*</span></label>
                        <input type="text" class="form-control" id="splitcharge_name[]" name="splitcharge_name[]" placeholder="Enter name of charge" value="" required>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label">Account<span class="requiredlabel">*</span></label>
                            <select name="splitchartofaccounts_id" id="splitchartofaccounts_id" class="formcontrol2" placeholder="Select" required>
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
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label">Charge Type<span class="requiredlabel">*</span></label>
                            <select name="splitcharge_type" id="splitcharge_type" class="formcontrol2 splitcharge_type" placeholder="Select" required>
                                <option value="">Select Bill Type</option>
                                <option value="fixed"> Fixed Amount</option>
                                <option value="units"> By Units</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label ratelabel">Amount<span class="requiredlabel">*</span></label>
                            <input type="text" class="form-control" name="splitrate" value="" required>
                        </div>
                    </div>             
            `);

            // Append the dynamic field div to the container
            dynamicFieldsContainer.append(dynamicFieldDiv);
            //     $('.cancel-field').click(function() {
                // Remove the parent dynamic field div when the Cancel button is clicked
            //         alert('clicked');
                //        $(this).closest('.dynamicadd').remove();
             //    });

            //    const $enddate = $("#enddate");
            //    $enddate.hide();
            $('.splitcharge_type').on('change', function() {
                var query = this.value;
                //  alert(query);
                // Select the label element
                var $rateLabel = $('.ratelabel');

                if (query === "fixed") {
                    $rateLabel.text('Amount');
                    $enddate.show();
                } else if (query === "units") {
                    $rateLabel.text('Rate');
                    $enddate.hide();
                }

            });
        });

            // Attach the click event handler for .cancel-field buttons
    dynamicFieldsContainer.on('click', '.cancel-field', removeDynamicField);


    });
</script>
@if(($routeParts[1] === 'create'))
<h5><b> Add Rent Details</b></h5>
<hr>

<div class="" id="rentinfo">
    @if(empty($existingRentCharge))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-danger float-end" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><a href="{{ url('skiprent') }}" class=" btn btn-outline-danger text-danger mb-0 nextBtn" id="nextBtn">
                <i class="mdi mdi mdi-arrow-right-bold-circle me-1"></i>
                <b>Skip To Next</b></a> if there is no Rent Charge</p>
    </div>
    @endif
    <form method="POST" action="{{ url('rent') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="col-md-6">

            <input type="hidden" class="form-control" id="property_id" name="property_id" value="{{$existingRentCharge->property_id ?? $lease->property_id ?? ''}}">
            <input type="hidden" class="form-control" id="unit_id" name="unit_id" value="{{$existingRentCharge->unit_id ?? $lease->unit_id ?? ''}}">
            <div class="form-group">
                <input type="hidden" class="form-control" id="charge_name" name="charge_name" value="Rent" readonly>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Rent Cycle<span class="requiredlabel">*</span></label>
                <select name="charge_cycle" id="charge_cycle" class="formcontrol2" placeholder="Select" required>
                   
                    @if (!empty($existingRentCharge->charge_cycle))
                    <option value="{{ $existingRentCharge->charge_cycle }}" selected>{{ $existingRentCharge->charge_cycle }}</option>
                    @endif
                    <!-- Add the predefined rent cycle options -->
                    <option value="Monthly" {{ (isset($rentcharge->charge_cycle) && $rentcharge->charge_cycle === 'Monthly') ? 'selected' : '' }}>Monthly</option>
                    <option value="Twomonths" {{ (isset($rentcharge->charge_cycle) && $rentcharge->charge_cycle === 'Twomonths') ? 'selected' : '' }}>Two Months</option>
                    <option value="Quaterly" {{ (isset($rentcharge->charge_cycle) && $rentcharge->charge_cycle === 'Quaterly') ? 'selected' : '' }}>Quarterly</option>
                    <option value="Halfyear" {{ (isset($rentcharge->charge_cycle) && $rentcharge->charge_cycle === 'Halfyear') ? 'selected' : '' }}>6 Months</option>
                    <option value="Year" {{ (isset($rentcharge->charge_cycle) && $rentcharge->charge_cycle === 'Year') ? 'selected' : '' }}>1 Year</option>

                </select>
            </div>
        </div>
        <!---------   ---->

        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Account<span class="requiredlabel">*</span></label>
                <select name="chartofaccounts_id" id="chartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                    <!-- Default Select Option -->
                    <option value="{{$defaultRentAccount->id ?? ''}}">{{$defaultRentAccount->account_name ?? 'Select Account/Rent Income Account'}}</option>
                    <!-- Dynamically add the existing rent charge account -->
                    @if (!empty($existingRentCharge->chartofaccounts_id))
                    <option value="{{ $existingRentCharge->chartofaccounts_id }}" selected>
                        {{ $existingRentCharge->chartofaccounts->account_name ?? 'Unknown Account' }}
                    </option>
                    @endif
                    <!-- Preselect based on $rentcharge -->
                    @if (!empty($rentcharge->chartofaccounts_id) && empty($existingRentCharge))
                    <option value="{{ $rentcharge->chartofaccounts_id }}" selected>
                        {{ $rentcharge->chartofaccounts->account_name ?? 'Unknown Account' }}
                    </option>
                    @endif
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

        <input type="hidden" class="form-control" name="charge_type" value="fixed" required readonly>

        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <div class="input-group">
                    <span class="input-group-text spanmoney">{{$sitesettings->site_currency}}</span>
                    <input type="number" class="form-control money" name="rate" value="{{$lease->unit->rent ?? ''}}" required>
                </div>
            </div>
        </div>
        @include('admin.CRUD.information', ['message' => $information ?? ''])

        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="{{$rentcharge->startdate ?? $lease->startdate ?? ''}}" required>
                <input type="hidden" class="form-control" id="recurring_charge" name="recurring_charge" value="yes">
            </div>
        </div>



        <br />
        <!--------- SPLIT RENT CHARGE ----------- -->
        <div class="addfields" style="margin-bottom:30px">
            @php
            $splitRentcharges = isset($existingSplitRentCharge) && !$existingSplitRentCharge->isEmpty()
            ? $existingSplitRentCharge
            : $sessioncharges;
            @endphp
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
                        <input type="text" class="form-control dynamic-field" id="splitcharge_name[]" name="splitcharge_name[]" value="{{ $splitCharge['charge_name']?? '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Account<span class="requiredlabel">*</span></label>
                        <select name="splitchartofaccounts_id[]" id="splitchartofaccounts_id" class="formcontrol2 dynamic-field" placeholder="Select" required>
                            <option value="{{$splitCharge['chartofaccounts_id'] ?? ''}}">
                            {{ $splitCharge['chartofaccounts']['account_name'] ?? 'Select Account' }}</option>
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
                        <select name="splitcharge_type[]" id="splitcharge_type" class="formcontrol2 dynamic-field" placeholder="Select" required>
                            <option value="{{$splitCharge['charge_type'] ?? ''}}">{{$splitCharge['charge_type'] ?? 'Select Account'}}</option>
                            <option value="fixed"> Fixed Amount</option>
                            <option value="units"> By Units</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label ratelabel">Amount<span class="requiredlabel">*</span></label>
                        <input type="text" class="form-control dynamic-field" name="splitrate[]" value="{{$splitCharge['rate'] ?? ''}}" required>
                    </div>
                </div>
            </div>
            @endforeach


            @endif
        </div>


        <div class="col-md-12" style="margin-bottom:30px">
            <h6 class=" split_rent"><i class="menu-icon mdi mdi-plus-circle me-1"></i> Split the Rent Charge
                <span class="text-muted">(Will be included in Rent Invoices & Payments)</span>
            </h6>
        </div>

        @include('admin.CRUD.wizardbuttons')
</div>
</form>
<!------------------------------                      --->
@elseif(($routeParts[1] === 'edit'))
<form method="POST" action="{{ url($routeParts[0].'/'.$unitcharge->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @method('PUT')
    @csrf
    <h6 style="text-transform: capitalize;"> Edit Rent Details &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink">Edit</a>
        @endif
    </h6>
    <hr>

    <div class="col-md-5">
        <div class="form-group">
            <label class="label">Rent Cycle<span class="requiredlabel">*</span></label>
            <h5>
                <small class="text-muted">
                    {{ $rentcharge->charge_cycle}}
                </small>
            </h5>
            <select name="charge_cycle" id="charge_cycle" class="formcontrol2" placeholder="Select" required>
                <option value="{{$rentcharge->charge_cycle ?? ''}}">{{$rentcharge->charge_cycle ?? 'Select Rent Cycle'}}</option>
                <option value="Monthly"> Monthly</option>
                <option value="Twomonths">Two Months</option>
                <option value="Quaterly">Quaterly</option>
                <option value="Halfyear">6 Months</option>
                <option value="Year">1 Year</option>

            </select>
        </div>
    </div>
    <!---------   ---->
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label class="label">Account<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $rentcharge->chartofaccounts->account_name}}
                    </small>
                </h5>
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

        <div class="col-md-5">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $rentcharge->rate}}
                    </small>
                </h5>
                <input type="text" class="form-control" name="rate" value="{{$rentcharge->rate ?? ''}}" required>
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
                    <h5>
                        <small class="text-muted">
                            {{ $splitCharge->charge_name}}
                        </small>
                    </h5>
                    <input type="text" class="form-control dynamic-field" id="splitcharge_name[]" name="splitcharge_name[]" value="{{ $splitCharge->charge_name ?? '' }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Account<span class=""></span></label>
                    <h5>
                        <small class="text-muted">
                            {{ $splitCharge->chartofaccounts->account_name}}
                        </small>
                    </h5>
                    <select name="splitchartofaccounts_id" id="splitchartofaccounts_id" class="formcontrol2 dynamic-field" placeholder="Select" required>
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
                    <h5>
                        <small class="text-muted">
                            {{ $splitCharge['charge_type']}}
                        </small>
                    </h5>
                    <select name="splitcharge_type" id="splitcharge_type" class="formcontrol2 dynamic-field" placeholder="Select" required>
                        <option value="{{$splitCharge['charge_type'] ?? ''}}">{{$splitCharge['charge_type'] ?? 'Select Account'}}</option>
                        <option value="fixed"> Fixed Amount</option>
                        <option value="units"> By Units</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label ratelabel">Amount<span class="requiredlabel">*</span></label>
                    <h5>
                        <small class="text-muted">
                            {{ $splitCharge['rate']}}
                        </small>
                    </h5>
                    <input type="text" class="form-control dynamic-field" name="splitrate" value="{{$splitCharge['rate'] ?? ''}}" required>
                </div>
            </div>
        </div>
        @endforeach


        @endif
    </div>
    <div class="col-md-12" style="margin-bottom:30px">
        <h6 class=" split_rent"><i class="menu-icon mdi mdi-plus-circle me-1"></i> Add to the Rent Charge
            <span class="text-muted">(Will be included in Rent Invoices & Payments)</span>
        </h6>
    </div>
    <hr>
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Rent Details</button>
    </div>



</form>

@endif


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
                        <input type="text" class="form-control dynamic-field" id="splitcharge_name[]" name="splitcharge_name[]" placeholder="Enter name of charge" value="" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label">Account<span class="requiredlabel">*</span></label>
                            <select name="splitchartofaccounts_id[]" id="splitchartofaccounts_id" class="formcontrol2 dynamic-field" placeholder="Select" required>
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
                            <select name="splitcharge_type[]" id="splitcharge_type[]" class="formcontrol2 splitcharge_type dynamic-field" placeholder="Select" required>
                                <option value="">Select Bill Type</option>
                                <option value="fixed"> Fixed Amount</option>
                                <option value="units"> By Units</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label ratelabel">Amount<span class="requiredlabel">*</span></label>
                            <input type="text" class="form-control dynamic-field" name="splitrate[]" value="" required>
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

<script>
    $(document).on('blur', 'input[name="splitcharge_name[]"]', function() {
        var inputValue = $(this).val();
        var propertyId = $('#property_id').val();
        var unitId = $('#unit_id').val(); // Get unit_id from hidden input
        var $inputField = $(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{url('api/check-chargename')}}",
            type: "POST",
            data: {
                charge_name: inputValue,
                property_id: propertyId, // Pass property_id to the server
                unit_id: unitId,
                _token: '{{csrf_token()}}'
            },
            dataType: 'json',
            success: function(data) {
                // Handle the success response if needed
            },
            error: function(xhr, status, error) {
                // Handle the error response here and show it to the user
                var errorMessage = xhr.responseJSON.message;
                $inputField.addClass('is-invalid');
                $inputField.siblings('.invalid-feedback').text(errorMessage).show();
                //  alert(errorMessage); // You can show the error message in an alert or any other way you prefer
            }
        });
    });
</script>
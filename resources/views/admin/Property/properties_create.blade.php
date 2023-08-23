@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

        <h4>New Property</h4>
        <hr>
    <form method="POST" action="{{ url('property/create') }}" id="myForm"  novalidate >
        @csrf
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Property Type<span class="requiredlabel">*</span></label>
                <select name="property_type" id ="property_type" class="formcontrol2" placeholder="Select" required>
                        <optgroup label="RESIDENTIAL">
                            <option value="Apartment">Apartment</option>
                            <option value="Town House">Town House</option>
                            <option value="Office">Office</option>                                
                        </optgroup>    
                        <optgroup label="COMMERCIAL">
                            <option value="Industrial">Industrial</option>
                            <option value="Office">Office</option>
                            <option value="Retail">Retail</option>
                            <option value="Shopping center">Shopping center</option>
                            <option value="Storage">Storage</option>
                            <option value="Parking space">Parking space</option>
                                
                        </optgroup>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Property Name<span class="requiredlabel">*</span></label>
                <input type="text" name="property_name" id ="company_name" class="form-control" value="{{ old('property_name') }}" required/>
            </div>
        </div>
      
        <h5>What is the street address?</h5>
        <hr>

        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Location<span class="requiredlabel">*</span></label>
                <input type="text" name="property_location" id ="property_location" class="form-control" value="{{ old('property_location') }}" required/>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Street Address<span class="requiredlabel">*</span></label>
                <input type="text" name="property_streetname" id ="property_streetname" class="form-control" value="{{ old('property_streetname') }}" required/>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Property Manager<span class="requiredlabel">*</span></label>
                <input type="text" name="property_manager" id ="property_manager" class="form-control" value="Not Set" required/>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Property Status</label>
                <select name="property_type" id ="property_type" class="formcontrol2" placeholder="Select" required>
                            <option value="Active">Active</option>
                            <option value="Closed">Closed</option>

                </select>                                
                          
            </div>
        </div>
       
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submit">Create Property</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        const $myForm = $("#myForm");
        const $requiredFields = $myForm.find('[required]');

        const validateForm = () => {
            let isValid = true;
            $requiredFields.each(function () {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').show();
                    $field.after('<div class="invalid-feedback">Please fill in this field.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').hide();
                }
            });

            return isValid;
        };

        $myForm.on("submit", function (event) {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>


@endsection
@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">

    <h4>New Tariff </h4>
    <hr>
    <form method="POST" action="{{ url('smsCredit') }}" class="myForm" novalidate>
        @csrf
    <div class="col-md-6">
        <div class="form-group">
        <label for="credit_type">Credit Type</label>
            <select name="credit_type" id="credit_type" class="formcontrol2">
                <option value="">Select Tariff Type</option>
                <option value="{{ \App\Models\SmsCredit::TYPE_PROPERTY }}">{{ \App\Models\SmsCredit::$statusLabels[\App\Models\SmsCredit::TYPE_PROPERTY] }}</option>
                <option value="{{ \App\Models\SmsCredit::TYPE_USER }}">{{ \App\Models\SmsCredit::$statusLabels[\App\Models\SmsCredit::TYPE_USER] }}</option>
                <option value="{{ \App\Models\SmsCredit::TYPE_INSTANCE }}">{{ \App\Models\SmsCredit::$statusLabels[\App\Models\SmsCredit::TYPE_INSTANCE] }}</option>
            </select>
        </div>

       
        <div class="form-group" id = 'property' style="display:none">
            <label class="label">Property <span class="requiredlabel">*</span></label>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" >
                    <option value="">Select Value</option>
                @foreach($properties as $property)
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                @endforeach
            </select>
        </div>
        
        
            <div class="form-group" id ="user" style="display:none">
                <label class="label">User <span class="requiredlabel">*</span></label>
                <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select" >
                    <option value="">Select Value</option>
                @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}}</option>
                @endforeach
            </select>
                
            </div>
       
            <div class="form-group" id="tariff" style="display:none">
                <label class="label">Tariff Rate <span class="requiredlabel">*</span></label>
                <input type="number" class="form-control" id="tariff" name="tariff" value="" required >
            </div>       
        <input type="hidden" class="form-control" id="available_credits" name="available_credits" value="10" required readonly>
        <input type="hidden" class="form-control" id="blocked_credits" name="blocked_credits" value="0" required readonly>
        <input type="hidden" class="form-control" id="used_credits" name="used_credits" value="0" required readonly>
        <div class="col-md-6" id="submitBtn" style="display:none">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Add Tariff</button>
        </div>
    </div>
    </form>
</div>
<script>
    $(document).ready(function() {
    // Listen for changes on the #send_to select input
    $('#credit_type').on('change', function() {
        let selectedValue = $(this).val(); // Get the selected value (contact or group)
        
        // Hide all divs initially
        $('#property').hide();
        $('#user').hide();
        $('#tariff').hide();
        $('#submitBtn').hide();

        if (selectedValue === '1') {
            // Show the contact div and message div when 'contact' is selected
            $('#property').show();
            $('#tariff').show();
            $('#submitBtn').show();
        } else if (selectedValue === '2') {
            // Show the group div and message div when 'group' is selected
            $('#user').show();
            $('#tariff').show();
            $('#submitBtn').show();
        }else if (selectedValue === '3') {
            // Show the group div and message div when 'group' is selected
            $('#tariff').show();
            $('#submitBtn').show();
        }

    });
});

</script>




@endsection
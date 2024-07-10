@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Deposit </h4>
    <hr>
    <form method="POST" action="{{ url('deposit') }}" class="myForm" novalidate>
        @csrf

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
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Name / Memo<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="" required list="list">
                
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Select User Type <span class="requiredlabel">*</span></label>
                <select name="model_type" id="assigned_type" class="formcontrol2 assigned_type" placeholder="Select" required>
                    <option value="">Select Type</option>
                    <option value="App\Models\User">User</option>
                    <option value="App\Models\Vendor">Vendor</option>
                </select>
            </div>
        </div>
        <div class="col-md-6" id="vendorSelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select Vendor<span class="requiredlabel">*</span></label>
                <select id="vendorSelectElement" class="formcontrol2 " placeholder="Select">
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                    <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                    @endforeach

                </select>
            </div>
        </div>
        <div class="col-md-6" id="userSelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select User<span class="requiredlabel">*</span></label>
                <select id="userSelectElement" class="formcontrol2 " placeholder="Select">

                    <option value="">Select Value</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}} -
                        @foreach($user->roles as $role)
                        {{ $role->name }}
                        @endforeach
                    </option>
                    @endforeach

                </select>
            </div>
        </div>
        <input type="hidden" class="form-control" id="status" name="status" value="unpaid" required readonly>
        <hr>
        @include('admin.Accounting.create_table')
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Deposit</button>
        </div>

    </form>
</div>

<script>
    $(document).ready(function() {
        $('.assigned_type').on('change', function() {
            var query = this.value.trim();

            console.log(query);

            var $user = $('#userSelect');
            var $vendor = $('#vendorSelect');
            var $userSelect = $('#userSelectElement');
            var $vendorSelect = $('#vendorSelectElement');
            var $submit = $('#submit');

            // Remove the name attribute from both selects initially
            $userSelect.removeAttr('name');
            $vendorSelect.removeAttr('name');

            if (query === "App\\Models\\Vendor") {
                $user.hide();
                $vendor.show();
                $vendorSelect.show().attr('name', 'model_id'); // Set the name attribute only for the visible select
                $submit.show();
            } else if (query === "App\\Models\\User") {
                $vendor.hide();
                $user.show();
                $userSelect.show().attr('name', 'model_id'); // Set the name attribute only for the visible select
                $submit.show();
            } else {
                $userSelect.hide();
                $vendorSelect.hide();
                $submit.hide();
            }
        });
    });
</script>

@endsection
@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    @if( $routeParts[1] === 'edit' && $instance->status !=='paid' && Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <h5>Edit Deposit
        <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    </h5>
    @endif

    <hr>
    <form method="POST" action="{{ url('deposit/'.$instance->id) }}" class="myForm" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="label">Property Name</label>
            <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{ $property->property_name }}
                </small>
            </h6>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                <option value="{{$property->id}}">{{$property->property_name}}</option>
            </select>
        </div>
        @if($instance->unit_id)
        <div class="form-group">
            <label class="label">Unit Number</label>
            <h5>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{ $instance->unit->unit_number }}
                </small>
            </h5>
            <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                <option value="{{$instance->unit->id}}">{{$instance->unit->unit_number}}</option>
            </select>
        </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Name / Memo<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->name }}
                    </small>
                </h5>
                <input type="text" class="form-control" id="name" name="name" value=" {{ $instance->name }}" required list="list">

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Select User Type <span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ class_basename($instance->model_type) }}
                    </small>
                </h5>
                <select name="model_type" id="assigned_type" class="formcontrol2 assigned_type" placeholder="Select" required>
                    <option value="App\Models\User" {{ $instance->model_type === 'App\Models\User' ? 'selected' : '' }}>User</option>
                    <option value="App\Models\Vendor" {{ $instance->model_type === 'App\Models\Vendor' ? 'selected' : '' }}>Vendor</option>
                </select>
            </div>
        </div>

        <div class="col-md-6" id="vendorSelect" style="{{ $instance->model_type === 'App\Models\Vendor' ? '' : 'display: none;' }}">
            <div class="form-group">
                <label class="label"> Select Vendor<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->model->name }}
                    </small>
                </h5>
                <select id="vendorSelectElement" class="formcontrol2" placeholder="Select" name="model_id">
                    <option value="{{$instance->model->id}}">{{$instance->model->name}}</option>
                    @foreach($vendors as $vendor)
                    <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6" id="userSelect" style ="{{ $instance->model_type === 'App\Models\User' ? '' : 'display: none;' }}">
            <div class="form-group">
                <label class="label"> Select User<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                    {{$instance->model->firstname}} {{$instance->model->lastname}}
                    </small>
                </h5>
                <select id="userSelectElement" class="formcontrol2" placeholder="Select" name="model_id">
                    <option value="{{$instance->model->id}}">{{$instance->model->firstname}} {{$instance->model->lastname}} -
                        @foreach($instance->model->roles as $role)
                        {{ $role->name }}
                        @endforeach
                    </option>
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
        @include('admin.Accounting.edit_table')
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit Deposit</button>
        </div>

    </form>
</div>

<script>
   $(document).ready(function() {
    // Function to toggle inputs based on the selected user type
    function toggleInputs(query) {
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
    }

    // Trigger change event on load for edit mode
    var initialQuery = $('#assigned_type').val().trim();
    toggleInputs(initialQuery);

    // Bind the change event
    $('.assigned_type').on('change', function() {
        var query = this.value.trim();
        toggleInputs(query);
    });
});

</script>

@endsection
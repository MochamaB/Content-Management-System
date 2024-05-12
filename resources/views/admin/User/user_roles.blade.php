<!------------ Create------------------->
@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('roleuser') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
<h4>Select Role</h4>
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Role Name<span class="requiredlabel">*</span></label>
        <select name="role" id="role" class="formcontrol2" required>
            <option value="{{$savedRole->id ?? ''}}">{{$savedRole->name ?? "Select A Role" }}</option>
            @foreach($roles as $item)
            <option value="{{$item->id}}">{{$item->name}}</option>
            @endforeach
            <select>
    </div>
    <br />

</div>
@include('admin.CRUD.wizardbuttons')
</form>

@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">{{$routeParts[0]}} Role &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Role Name<span class="requiredlabel">*</span></label>
        <h5>
                    <small class="text-muted">
                    {{ $editUser->roles->first()->name ?? '' }}
                    </small>
                </h5>
        <select name="role" id="role" class="formcontrol2" required>
            <option value=" {{ $user->roles->first()->id  ?? ''}}"> {{ $editUser->roles->first()->name ?? '' }}</option>
            @foreach($roles as $item)
            <option value="{{$item->id ?? ''}}">{{$item->name ?? ''}}</option>
            @endforeach
            <select>
    </div>
</div>
<br />
<div class="col-md-5">
                <div class="row">
                    <div class="col-md-6 ">
                        <button type="submit" class="btn btn-primary  btn-lg text-white mb-0 me-0 submitBtn" id="">Edit: User Role</button>
                    </div>
                </div>
            </div>
@endif
<script>
        $(document).ready(function () {
            // Initially hide the Property Access tab
            $(".propertyaccess").show();
            $("#role").on("change", function () {
                // Get the selected role
                var selectedRole = $(this).find(":selected").text();
                
             
                // Check if the selected role is "Tenant"
                if (selectedRole === "Tenant") {
                 
                    // Hide the Property Access tab
                    $(".propertyaccess").hide();
                } else {
                    // Show the Property Access tab
                    $(".property-access").show();
                }
            });

        });
        
    </script>
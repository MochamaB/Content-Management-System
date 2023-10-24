@if(($routeParts[1] === 'create'))
<h4><b> Add Tenant Cosigner Details</b></h4>
<hr>
<form method="POST" action="{{ url('cosigner') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

    <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{$lease->user_id ?? ''}}">

    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Relationship with Tenant<span class="requiredlabel">*</span></label>
            <input type="text" class="form-control" id="user_relationship" name="user_relationship" value="{{$tenantdetails->user_relationship ?? ''}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Name<span class="requiredlabel">*</span></label>
            <input type="text" class="form-control" id="emergency_name" name="emergency_name" value="{{$tenantdetails->emergency_name ?? ''}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Phone Number<span class="requiredlabel">*</span></label>
            <input type="tel" class="form-control" id="emergency_number" name="emergency_number" value="{{$tenantdetails->emergency_number ?? ''}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Email<span class="requiredlabel">*</span></label>
            <input type="email" class="form-control" id="emergency_email" name="emergency_email" value="{{$tenantdetails->emergency_email ?? ''}}" required>
        </div>
    </div>

    @include('admin.CRUD.wizardbuttons')

</form>
@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;"> Edit Tenant Cosigner Details &nbsp;
    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <a href="" class="editLink">Edit</a>
</h4>
@endif
<hr>

<div class="col-md-6">
    <div class="form-group">
        <label class="label">Relationship with Tenant<span class="requiredlabel">*</span></label>
        <h5>
            <small class="text-muted">
                {{ $tenantdetails->user_relationship}}
            </small>
        </h5>
        <input type="text" class="form-control" id="user_relationship" name="user_relationship" value="{{$tenantdetails->user_relationship ?? ''}}" required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Name<span class="requiredlabel">*</span></label>
        <h5>
            <small class="text-muted">
                {{ $tenantdetails->emergency_name}}
            </small>
        </h5>
        <input type="text" class="form-control" id="emergency_name" name="emergency_name" value="{{$tenantdetails->emergency_name ?? ''}}" required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Phone Number<span class="requiredlabel">*</span></label>
        <h5>
            <small class="text-muted">
                {{ $tenantdetails->emergency_number}}
            </small>
        </h5>
        <input type="tel" class="form-control" id="emergency_number" name="emergency_number" value="{{$tenantdetails->emergency_number ?? ''}}" required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Email<span class="requiredlabel">*</span></label>
        <h5>
            <small class="text-muted">
                {{ $tenantdetails->emergency_email}}
            </small>
        </h5>
        <input type="email" class="form-control" id="emergency_email" name="emergency_email" value="{{$tenantdetails->emergency_email ?? ''}}" required>
    </div>
</div>
<hr>
<div class="col-md-6">
    <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Cosigner Details</button>
</div>





@endif
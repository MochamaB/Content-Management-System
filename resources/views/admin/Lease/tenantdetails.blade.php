<h4><b> Add Tenant Cosigner Details</b></h4>
<hr>
<form method="POST" action="{{ url('tenantdetails') }}" id="myForm" enctype="multipart/form-data" novalidate>
    @csrf
   
    <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{$lease->user_id ?? ''}}" >

<div class="col-md-6">
    <div class="form-group">
        <label class="label">Relationship with Tenant<span class="requiredlabel">*</span></label>
        <input type="text" class="form-control" id="user_relationship" name="user_relationship" value= "{{$tenantdetails->user_relationship ?? ''}}" required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Name<span class="requiredlabel">*</span></label>
        <input type="text" class="form-control" id="emergency_name" name="emergency_name" value= "{{$tenantdetails->emergency_name ?? ''}}" required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Phone Number<span class="requiredlabel">*</span></label>
        <input type="tel" class="form-control" id="emergency_number" name="emergency_number" value= "{{$tenantdetails->emergency_number ?? ''}}"  required>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Email<span class="requiredlabel">*</span></label>
        <input type="email" class="form-control" id="emergency_email" name="emergency_email" value= "{{$tenantdetails->emergency_email ?? ''}}"  required>
    </div>
</div>



<div class="col-md-5">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous: Lease</button>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 " id="">Next:Rent Details</button>
        </div>
    </div>
</div>
    
</form>
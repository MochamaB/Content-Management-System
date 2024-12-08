<h5>Complete Move Out</h5>
<hr>
<form method="POST" action="{{ url('lease/' . $lease->id . '/completeMoveOut') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <p>When you complete the lease moveout the following actions will happen </p>
    <ul>
        <li>The lease status will change to terminated</li>
        <li>The Security Deposit will be adjusted to cater for the total costs of repair</li>
        <li>Tenant user account will status will be inactive</li>
       
    </ul>

    <div class="form-group">
        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="send_welcome_email" id="send_welcome_email" value="1" checked="">
                Send Lease Agreement Termination email to tenant
                <i class="input-helper"></i></label>
        </div>
        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="send_welcome_text" id="send_welcome_text" value="1" checked="">
                Send Lease Agreement Termination text to tenant
                <i class="input-helper"></i></label>
        </div>

    </div>

    @include('admin.CRUD.wizardbuttons')
</form>
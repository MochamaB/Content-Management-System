
@if(($routeParts[1] === 'create'))
<h4>Login Acess</h4>
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Password<span class="requiredlabel">*</span></label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" value="property123" required />
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">
                    <i class="mdi mdi-eye" id="password-toggle"></i>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Confirm Password<span class="requiredlabel">*</span></label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" value="property123" required />
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">
                    <i class="mdi mdi-eye" id="password_confirmation-toggle"></i>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-3">
        <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous:Contact Info</button>
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0 nextBtn" id="nextBtn">Next:Property Access</button>
    </div>
</div>
@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">{{$routeParts[0]}} Password Reset &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Password<span class="requiredlabel">*</span></label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" value="property123" required />
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">
                    <i class="mdi mdi-eye" id="password-toggle"></i>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Confirm Password<span class="requiredlabel">*</span></label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" value="property123" required />
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">
                    <i class="mdi mdi-eye" id="password_confirmation-toggle"></i>
                </span>
            </div>
        </div>
    </div>
</div>


<br />

                    <div class="col-md-6  ">
                        <button type="submit" class="btn btn-primary  btn-lg text-white mb-0 me-0 submitBtn" id="">Edit: Reset Password</button>
                    </div>
   

@endif
<script>
    $(document).ready(function() {
        $("#password-toggle").click(function() {
            const passwordInput = $("#password");
            if (passwordInput.attr("type") === "password") {
                passwordInput.attr("type", "text");
            } else {
                passwordInput.attr("type", "password");
            }
        });
        $("#password_confirmation-toggle").click(function() {
            const passwordInput = $("#password_confirmation");
            if (passwordInput.attr("type") === "password") {
                passwordInput.attr("type", "text");
            } else {
                passwordInput.attr("type", "password");
            }
        });
    });
</script>
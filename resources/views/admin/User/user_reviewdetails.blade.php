@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('user') }}" class="myForm" enctype="multipart/form-data" novalidate>
  @csrf
  <h4>Complete Registration</h4>
  <hr>
  <h6 class="">Choose below how to notify the new user:</h6>


  <div class="form-group">
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="send_welcome_email" id="send_welcome_email" value="1" checked="">
        Send welcome email to new user
        <i class="input-helper"></i></label>
    </div>
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="send_welcome_text" id="send_welcome_text" value="1"  checked="">
        Send welcome text message to user
        <i class="input-helper"></i></label>
    </div>

  </div>
  @endif
  @include('admin.CRUD.wizardbuttons')
</form>
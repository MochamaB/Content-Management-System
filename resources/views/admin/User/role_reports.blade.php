@if(($routeParts[1] === 'create'))

<h4>Report Access</h4>
<hr>
    <div class="col-md-5">
      <div class="row">
        <div class="col-md-6">
          <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn" id="previousBtn">Previous: Module</button>
        </div>
        <div class="col-md-6">
          <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Create Role</button>
        </div>
      </div>
    </div>

@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">Report Acess &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
    <div class="col-md-5">
      <div class="row">
        <div class="col-md-6">
          <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Report Access</button>
        </div>
      </div>
    </div>


@endif
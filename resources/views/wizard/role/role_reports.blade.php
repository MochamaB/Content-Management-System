@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('assignreports') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
<h4>Report Access</h4>
<hr>
@include('admin.CRUD.wizardbuttons')
</form>

@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">Report Acess &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a></h4>
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
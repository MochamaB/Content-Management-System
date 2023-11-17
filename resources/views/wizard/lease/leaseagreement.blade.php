@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('lease') }}" class="myForm" enctype="multipart/form-data" novalidate>
@csrf

<div class="col-md-8">
            <div class="form-group">
                <label class="label">Upload Lease Agreement<span class="requiredlabel">*</span></label>
                <input type="file" name="file" class="form-control" id="logo" required/>
                <img id="logo-image-before-upload" src="{{ url('resources/uploads/images/noimage.jpg') }}" style="height: 200px; width: 200px;">
            </div>
        </div>

@include('admin.CRUD.wizardbuttons')
</form>
@endif
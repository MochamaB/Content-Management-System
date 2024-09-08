<!------------ Create------------------->
@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('userinfo') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <h5>Contact Information</h5>
    <hr>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">First Name<span class="requiredlabel">*</span></label>
            <input type="text" name="firstname" id="name" class="form-control" value="{{old('firstname') ?? '' }}" required />
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Last Name<span class="requiredlabel">*</span></label>
            <input type="text" name="lastname" id="name" class="form-control" value="{{old('lastname') ?? '' }}" required />
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Email<span class="requiredlabel">*</span></label>
            <input type="text" name="email" id="name" class="form-control" value="{{ old('email') ?? '' }}" required />
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Phone Number<span class="requiredlabel">*</span></label>
            <input type="tel" name="phonenumber" id="name" class="form-control" value="{{old('phonenumber') ?? '' }}" required />

            <input type="hidden" name="idnumber" id="name" class="form-control" value="00000000" required />
            <input type="hidden" name="password" id="name" class="form-control" value="property123" required />
            <input type="hidden" name="status" id="name" class="form-control" value="Active" required />
            <input type="hidden" name="profilepicture" id="name" class="form-control" value="avatar.png" required />
        </div>
    </div>

    @include('admin.CRUD.wizardbuttons')
</form>

@elseif(($routeParts[1] === 'edit'))
@php
$avatarUrl = $editUser->getFirstMediaUrl('avatar');
$avatarUrl = empty($avatarUrl) ? 'uploads/images/avatar.png' : $avatarUrl;
@endphp
<form method="POST" action="{{ url($routeParts[0].'/'.$editUser->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @method('PUT')
    @csrf

    <h6 style="text-transform: capitalize;">{{$routeParts[0]}} Information &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink">  <i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i> </a>
    </h6>
    @endif
    <hr>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Profile Picture</label>
            <h5>
                <small class="text-muted">
                    {{ $editUser->profilepicture }}
                </small>
            </h5>
            <input type="file" name="profilepicture" value="{{$editUser->profilepicture }}" class="form-control" id="logo" /></br>
            <img id="logo-image-before-upload" src="{{  url($avatarUrl) }}" style="height: 170px; width: 170px;">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">First Name<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $editUser->firstname }}
                    </small>
                </h5>
                <input type="text" name="firstname" id="name" class="form-control" value="{{ $editUser->firstname }}" required />
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Last Name<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $editUser->lastname }}
                    </small>
                </h5>
                <input type="text" name="lastname" id="name" class="form-control" value=" {{ $editUser->lastname }}" required />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Email<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $editUser->email }}
                    </small>
                </h5>
                <input type="text" name="email" id="name" class="form-control" value=" {{ $editUser->email }}" required />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Phone Number<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $editUser->phonenumber }}
                    </small>
                </h5>
                <input type="tel" name="phonenumber" id="name" class="form-control" value=" {{ $editUser->phonenumber }}" required />
            </div>
        </div>
    </div>


    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn">Edit: User Info</button>
            </div>
        </div>
    </div>

</form>

@endif
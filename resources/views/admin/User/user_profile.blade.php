@extends('layouts.admin.admin')

@section('content')
@php
$avatarUrl = Auth::user()->getFirstMediaUrl('avatar');
$avatarUrl = empty($avatarUrl) ? 'uploads/images/avatar.png' : $avatarUrl;
@endphp
<div class="row">

    <div class="col-md-3">
        <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">

            <h4><b>User Details </b>
                @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                <a href="" class="editLink">Edit</a>
            </h4>
            @endif
            </h4>
            <hr>
            <form method="POST" action="{{ url($routeParts[0].'/'.$user->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
                @method('PUT')
                @csrf
                <div class="">
                    <div class="form-group">
                        <label class="label">Profile Picture</label>
                        <input type="file" name="profilepicture" value="" class="form-control" id="logo" /></br>
                        <img id="logo-image-before-upload" src="{{  url($avatarUrl) }}" style="height: 170px; width: 170px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label class="label">First Name<span class="requiredlabel">*</span></label>
                            <h5>
                                <small class="text-muted">
                                    {{ $showUser->firstname }}
                                </small>
                            </h5>
                            <input type="text" name="firstname" id="name" class="form-control" value="{{ $showUser->firstname }}" required />
                        </div>
                    </div>

                    <div class="col-md-11">
                        <div class="form-group">
                            <label class="label">Last Name<span class="requiredlabel">*</span></label>
                            <h5>
                                <small class="text-muted">
                                    {{ $showUser->lastname }}
                                </small>
                            </h5>
                            <input type="text" name="lastname" id="name" class="form-control" value=" {{ $showUser->lastname }}" required />
                        </div>
                    </div>
                    <div class="col-md-11">
                        <div class="form-group">
                            <label class="label">Email<span class="requiredlabel">*</span></label>
                            <h5>
                                <small class="text-muted">
                                    {{ $showUser->email }}
                                </small>
                            </h5>
                            <input type="text" name="email" id="name" class="form-control" value=" {{ $showUser->email }}" required />
                        </div>
                    </div>
                    <div class="col-md-11">
                        <div class="form-group">
                            <label class="label">Phone Number<span class="requiredlabel">*</span></label>
                            <h5>
                                <small class="text-muted">
                                    {{ $showUser->phonenumber }}
                                </small>
                            </h5>
                            <input type="tel" name="phonenumber" id="name" class="form-control" value=" {{ $showUser->phonenumber }}" required />
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


        </div>

    </div>
    <div class="col-md-9">

        <div class=" contwrapper">
            @include('admin.CRUD.tabs_horizontal')

        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        // Elements
        const $editLink = $(".editLink");
        const $editFields = $(".form-control");
        const $editSelect = $(".formcontrol2");
        const $edittextarea = $(".textformcontrol");
        const $Display = $(".text-muted");
        const $nextBtn = $("#nextBtn");
        const $submitBtn = $(".submitBtn");

        // Hide edit fields and "Make Changes" button on page load
        $editFields.hide();
        $editSelect.hide();
        $edittextarea.hide();
        $nextBtn.hide();
        $submitBtn.hide();

        // "Edit" link click event
        // "Edit" link click event
        $editLink.on("click", function(event) {
            event.preventDefault();
            // Toggle edit fields and buttons visibility
            $editFields.toggle();
            $editSelect.toggle();
            $edittextarea.toggle();
            $Display.toggle();
            $nextBtn.toggle();
            $submitBtn.toggle();

            // Toggle "Edit" link text between "Edit" and "Cancel"
            $editLink.text(function(index, text) {
                return text === "Edit" ? "Cancel" : "Edit";
            });
        });

        // You can add logic for "Save" and "Cancel" buttons here if needed
        // For example, you can handle form submission to update the data in the database
    });
</script>
@endsection
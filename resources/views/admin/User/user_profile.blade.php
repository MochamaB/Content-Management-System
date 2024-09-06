@extends('layouts.admin.admin')

@section('content')
@php
$avatarUrl = Auth::user()->getFirstMediaUrl('avatar');
$avatarUrl = empty($avatarUrl) ? 'uploads/images/avatar.png' : $avatarUrl;
@endphp
<div class=" contwrapper mb-2" style="background-color: #dfebf3;border: 1px solid #7fafd0;">

    <div class="row">
        <h6><b>User Details </b>
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="{{url('user/'.$showUser->id.'/edit')}}" class=""> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil mdi-16px text-primary" style="font-size:18px"></i></a>
        @endif
        </h6>
        <hr>
        <div class="col-md-3">
            <div class="dropdown-header text-center">
                <img class="img-thumbnail rounded-circle" src="{{  url($avatarUrl) }}" alt="Profile image" style="height: 150px; width: 150px;">
                <p class="mb-1 mt-3 font-weight-semibold"> {{ $showUser->firstname }} {{ $showUser->lastname }}</p>
                <p class="fw-light text-muted mb-0"> {{ $showUser->email }}</p>
                <p class="fw-light text-muted mb-0"> {{ $showUser->phonenumber }}</p>
            </div>
        </div>
        <div class="col-md-9">
        </div>



    </div>
   
</div>
<div class=" contwrapper">
    @include('admin.CRUD.tabs_horizontal')

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
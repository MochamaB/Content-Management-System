@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4 style="text-transform: capitalize;"> System Settings &nbsp;
        @if( Auth::user()->can('setting.system') || Auth::user()->id === 1)
        <a href="" class="editLink"> Edit</a>
    </h4>
    @endif
    <hr>
    <form method="POST" action="{{ url('update-systemsettings') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        @foreach($envVariables as $key => $value)
        <div class="col-md-6">
            <div class="form-group">

                <label class="label">{{ $key }}

                </label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{$value}}
                    </small>
                </h5>

                <input type="text" class="form-control" id="" value="{{ $value }}" name="{{ $key }}">
                </br>

            </div>
        </div>
        @endforeach
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="submit">Edit System Settings</button>
        </div>
    </form>
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
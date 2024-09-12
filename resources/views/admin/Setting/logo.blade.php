@if(empty($sitesettings->site_name))

<button class="btn btn-primary btn-lg text-white mb-0 me-0" style="float:right" type="button" onclick="window.location='{{ url("/Website/create") }}'">
    Add Logo</button>
<br /><br />
<div class=" contwrapper">

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <h5> Website Logos have not been added.</h5>
            <img class="svgimage float-center" src="{{ url('uploads/vectors/settings.svg') }}">
        </div>
        <div class="col-md-3"></div>
    </div>


</div>
@else

<form method="POST" action="{{ url('Website/1') }}" class="myForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class=" contwrapper">

        <h4> Logos
            @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
            <a href="#" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
        </h4>
        @endif
        </h4>

        <hr>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Company Logos</label>
                <input type="file" name="company_logo" value="{{ $sitesettings->company_logo }}" class="form-control" id="logo" />
                <img id="logo-image-before-upload" src="{{ $sitesettings->getFirstMediaUrl('logo') }}" style="height: 200px; width: 200px;">
            </div>
            <div class="form-group">
                <label class="label">flavicon</label>
                <input type="file" name="company_flavicon" value="{{ old('company_flavicon') }}" class="form-control" id="flavicon" />
                <img id="flavicon-image-before-upload" src="{{ $sitesettings->getFirstMediaUrl('flavicon') }}" style="height: 100px; width: 90px;">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submitBtn">Edit Logos</button>
            </div>

        </div>

    </div>
    <!----------- ------------------>
    <!----------- ------------------>


    @endif
</form>

<script>
    $(document).ready(function() {
        // Elements
        const $editLink = $(".editLink");
        const $editFields = $(".form-control");
        const $Display = $(".text-muted");
        const $nextBtn = $("#nextBtn");
        const $submitBtn = $("#submitBtn");

        // Hide edit fields and "Make Changes" button on page load
        $editFields.hide();
        $nextBtn.hide();
        $submitBtn.hide();

        // "Edit" link click event
        $editLink.on("click", function(event) {
            event.preventDefault();
            // Show edit fields
            $editFields.show();
            $Display.hide();
            $nextBtn.show();
            $submitBtn.show();
        });

        // You can add logic for "Save" and "Cancel" buttons here if needed
        // For example, you can handle form submission to update the data in the database
    });
</script>
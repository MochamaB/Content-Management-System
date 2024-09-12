@if(empty($sitesettings->site_name))

<button class="btn btn-primary btn-lg text-white mb-0 me-0" style="float:right" type="button" onclick="window.location='{{ url("/Website/create") }}'">
    Add Site Branding</button>
<br /><br />
<div class=" contwrapper">
   
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
        <h5> Website Branding settings have not been added.</h5>
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

                <h4>General Settings
                    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                    <a href="#" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
                </h4>
                @endif
                <hr>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Site Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->site_name }}
                            </small> </h5>
                        <input type="text" name="site_name" id="site_name" class="form-control" value="{{ $sitesettings->site_name }}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Company Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_name }}
                            </small> </h5>
                        <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $sitesettings->company_name }}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">About Company</label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_aboutus }}
                            </small> </h5>
                        <textarea id="company_aboutus" name="company_aboutus" class="form-control" rows="4" cols="50" autofocus>
                        {{ $sitesettings->company_aboutus }}
                        </textarea>
                    </div>
                </div>

                <h4>Contacts</h4>
                <hr>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Telephone<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_telephone }}
                            </small> </h5>
                        <input type="text" name="company_telephone" id="company_telephone" class="form-control" value="{{ $sitesettings->company_telephone }}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Company Email<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_email }}
                            </small> </h5>
                        <input type="text" name="company_email" id="company_email" class="form-control" value="{{$sitesettings->company_email }}" required />
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Location/Street Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_location }}
                            </small> </h5>
                        <input type="text" name="company_location" id="company_location" class="form-control" value="{{ $sitesettings->company_location  }}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Google Map</label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_googlemaps }}
                            </small> </h5>
                        <input type="text" name="company_googlemaps" id="company_googlemaps" class="form-control" value="{{  $sitesettings->company_googlemaps }}" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Currency <span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->site_currency }}
                            </small> </h5>
                        <input type="text" name="site_currency" id="site_currency" class="form-control" value="{{  $sitesettings->site_currency}}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">

                        <label class="label">Banner Description</label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->banner_desc }}
                            </small> </h5>
                        <textarea id="banner_desc" name="banner_desc" class="form-control" rows="4" cols="50">
                        {{ $sitesettings->banner_desc }}
                        </textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn">Edit Branding</button>
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
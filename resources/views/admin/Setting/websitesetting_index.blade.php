@extends('layouts.admin.admin')

@section('content')

<ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="ex1-tab-1" data-bs-toggle="tab" href="#ex1-tabs-1" role="tab" aria-controls="ex1-tabs-1" aria-selected="true">General Settings</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="ex1-tab-2" data-bs-toggle="tab" href="#ex1-tabs-2" role="tab" aria-controls="ex1-tabs-2" aria-selected="false">
            Logos</a>
    </li>
</ul>
@if(empty($sitesettings))

<button class="btn btn-primary btn-lg text-white mb-0 me-0" style="float:right" type="button" onclick="window.location='{{ url("/websitesetting/create") }}'">
    Add Site Information</button>
<br /><br /><br />
<div class=" contwrapper">
   
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
        <h3>Website settings have not been found.</h3>
        <img class="svgimage float-center" src="{{ url('uploads/vectors/settings.svg') }}">
        </div>
        <div class="col-md-3"></div>
    </div>
   

</div>
@else
<form method="POST" action="{{ url('websitesetting/1') }}" class="myForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="tab-content" id="ex1-content">
        <!----------- ------------------>
        <div class="tab-pane fade show active" id="ex1-tabs-1" role="tabpanel" aria-labelledby="ex1-tab-1">
            <div class=" contwrapper">

                <h4>General Settings
                    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                    <a href="#" class="editLink">Edit</a>
                </h4>
                @endif
                <hr>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Site Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->site_name }}
                            </small> </h5>
                        <input type="text" name="site_name" id="site_name" class="form-control" value="{{ $sitesettings->site_name }}" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Company Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_name }}
                            </small> </h5>
                        <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $sitesettings->company_name }}" required />
                    </div>
                </div>
                <div class="col-md-4">
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

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Telephone<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_telephone }}
                            </small> </h5>
                        <input type="text" name="company_telephone" id="company_telephone" class="form-control" value="{{ $sitesettings->company_telephone }}" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Company Email<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_email }}
                            </small> </h5>
                        <input type="text" name="company_email" id="company_email" class="form-control" value="{{$sitesettings->company_email }}" required />
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Location/Street Name<span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_location }}
                            </small> </h5>
                        <input type="text" name="company_location" id="company_location" class="form-control" value="{{ $sitesettings->company_location  }}" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Google Map</label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->company_googlemaps }}
                            </small> </h5>
                        <input type="text" name="company_googlemaps" id="company_googlemaps" class="form-control" value="{{  $sitesettings->company_googlemaps }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Currency <span class="requiredlabel">*</span></label>
                        <h5><small class="text-muted">
                                {{ $sitesettings->site_currency }}
                            </small> </h5>
                        <input type="text" name="site_currency" id="site_currency" class="form-control" value="{{  $sitesettings->site_currency}}" required />
                    </div>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn">Next</button>
                </div>
            </div>

        </div>
        <!----------- ------------------>
        <div class="tab-pane fade" id="ex1-tabs-2" role="tabpanel" aria-labelledby="ex1-tab-2">
            <div class=" contwrapper">

                <h4> Logos
                    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                    <a href="#" class="editLink">Edit</a>
                </h4>
                @endif
                </h4>

                <hr>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="label">Website Logos</label>
                        <input type="file" name="company_logo" value="{{ $sitesettings->company_logo }}" class="form-control" id="logo"  />
                        <img id="logo-image-before-upload" src="{{ $sitesettings->getFirstMediaUrl('logo') }}" style="height: 200px; width: 200px;">
                    </div>
                    <div class="form-group">
                        <label class="label">flavicon</label>
                        <input type="file" name="company_flavicon" value="{{ old('company_flavicon') }}" class="form-control" id="flavicon" />
                        <img id="flavicon-image-before-upload" src="{{ $sitesettings->getFirstMediaUrl('flavicon') }}" style="height: 100px; width: 90px;">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submitBtn">Save Settings</button>
                    </div>

                </div>

            </div>
        </div>
        <!----------- ------------------>

    </div>
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
<script>
    $(document).ready(function() {
        const $tabs = $("#ex1 .nav-link");
        const $tabContents = $("#ex1-content .tab-pane");
        let currentTab = 0;

        const showTab = (tabIndex) => {
            $tabs.removeClass("active").eq(tabIndex).addClass("active");
            $tabContents.removeClass("show active").eq(tabIndex).addClass("show active");
        };

        const validateTab = (tabIndex) => {
            const $currentTab = $tabContents.eq(tabIndex);
            const $requiredFields = $currentTab.find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').remove();
                    $field.after('<div class="invalid-feedback">This field is required.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').remove();
                }
            });

            return isValid;
        };

        $("#nextBtn").on("click", function() {
            if (!validateTab(currentTab)) {
                return;
            }

            currentTab++;
            if (currentTab >= $tabs.length) {
                currentTab = $tabs.length - 1;
            }
            showTab(currentTab);
        });

        // Show the first tab on page load
        showTab(currentTab);
    });
</script>


@endsection
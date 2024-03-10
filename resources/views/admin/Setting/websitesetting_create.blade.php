@extends('layouts.admin.admin')

@section('content')
<ul class="nav nav-tabs mb-3" id="ex1" role="tablist" >
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="ex1-tab-1" data-bs-toggle="tab" href="#ex1-tabs-1" role="tab" aria-controls="ex1-tabs-1"aria-selected="true"
      >General Settings</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="ex1-tab-2"data-bs-toggle="tab"href="#ex1-tabs-2" role="tab" aria-controls="ex1-tabs-2" aria-selected="false">
        Logos</a >
  </li>
</ul>
<form method="POST" action="{{ url($routeParts[0]) }}" enctype="multipart/form-data">
        @csrf

<div class="tab-content" id="ex1-content">
    <!----------- ------------------>
  <div class="tab-pane fade show active" id="ex1-tabs-1" role="tabpanel" aria-labelledby="ex1-tab-1" >
        
        <div class=" contwrapper">

            <h4>General Settings</h4>
            <hr>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Site Name<span class="requiredlabel">*</span></label>
                            <input type="text" name="site_name" id ="site_name" class="form-control" value="{{ old('site_name') }}" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Company Name<span class="requiredlabel">*</span></label>
                            <input type="text" name="company_name" id ="company_name" class="form-control" value="{{ old('company_name') }}" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">About Company</label>
                            <textarea id="company_aboutus" name="company_aboutus" class="form-control"  autofocus>
                            Write description about the company
                            </textarea>
                        </div>
                    </div>
                   
                    <h4>Contacts</h4>
                    <hr>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Telephone<span class="requiredlabel">*</span></label>
                            <input type="text" name="company_telephone" id ="company_telephone" class="form-control" value="{{ old('company_telephone') }}" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Company Email<span class="requiredlabel">*</span></label>
                            <input type="text" name="company_email" id ="company_email" class="form-control" value="{{ old('company_email') }}" required/>
                        </div>
                    </div>

                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Location/Street Name<span class="requiredlabel">*</span></label>
                            <input type="text" name="company_location" id ="company_location" class="form-control" value="{{ old('company_location') }}" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Google Map</label>
                            <input type="text" name="company_googlemaps" id ="company_googlemaps" class="form-control" value="{{ old('company_googlemaps') }}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Currency <span class="requiredlabel">*</span></label>
                            <input type="text" name="site_currency" id ="site_currency" class="form-control" value="{{ old('site_currency') }}" required/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Banner Description</label>
                            <textarea id="banner_desc" class="form-control" name="banner_desc" rows="4">
                            Write the banner description
                            </textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn">Go to Next</button>
                    </div>
        </div>
                              
  </div>
  <!----------- ------------------>
  <div class="tab-pane fade" id="ex1-tabs-2" role="tabpanel" aria-labelledby="ex1-tab-2">
        <div class=" contwrapper">

            <h4>Add Logos</h4>
            <hr>

            <div class="col-md-4">
                        <div class="form-group">
                            <label class="label">Website Logos</label>
                            <input type="file" name="company_logo" value="{{ old('company_logo') }}" class="form-control" id="logo"  required/>
                            <img id="logo-image-before-upload" src="{{ url('uploads/images/noimage.jpg') }}"
                                            style="height: 200px; width: 200px;">
                        </div>
                        <div class="form-group">
                            <label class="label">flavicon</label>
                            <input type="file" name="company_flavicon" value="{{ old('company_flavicon') }}" class="form-control" id="flavicon" />
                            <img id="flavicon-image-before-upload" src="{{ url('uploads/Images/noimage.jpg') }}"
                                            style="height: 100px; width: 90px;">  
                        </div>
                        <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn">Save Settings</button>
                        </div>
                        
                    </div>

        </div>
    
    
  </div>
  <!----------- ------------------>
</div>
</form>

<script>
        $(document).ready(function () {
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

                $requiredFields.each(function () {
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

            $("#nextBtn").on("click", function () {
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
@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

            <h4>Website Settings</h4>
            <hr><br/>
            <div class="row">
            <div class="col-md-6" style="padding-bottom:30px;">
            <a class="" href="{{ url('/websitesetting') }}"><h4>Application Branding</h4></a>
                  <div class="media">
                    <i class="ti-world icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                    <div class="media-body">
                      <p class="card-text">Customize your company's name or logo and set profile photo settings..</p>
                    </div>
                  </div>
            </div>
            <div class="col-md-6" style="padding-bottom:30px;">

                  <a class="" href="{{ url('/slider') }}"><h4>Sliders</h4></a>
                  <div class="media">
                    <i class="ti-layers icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                    <div class="media-body">
                      <p class="card-text">Customize slider and add photos and texts to slides..</p>
                    </div>
                  </div>
            </div>
            <div class="col-md-6" style="padding-bottom:30px;">

                  <a class="" href="{{ url('/testimonial') }}"><h4>Testimonials</h4></a>
                  <div class="media">
                    <i class="ti-thought icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                    <div class="media-body">
                      <p class="card-text">Add testimonials from the clients using your services..</p>
                    </div>
                  </div>
            </div>
            <h4>Property Settings</h4>
            <hr><br/>
            <div class="col-md-6" style="padding-bottom:30px;">
                <a class="" href="{{ url('/propertytype') }}"><h4>Property Types</h4></a>
                <div class="media ">
                  <i class="ti-home icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                  <div class="media-body">
                    <p class="card-text">Add Categories of Property Types..</p>
                  </div>
                </div>
            </div>
            <div class="col-md-6" style="padding-bottom:30px;">
                <a class="" href="{{ url('/amenity') }}"><h4>Property Access</h4></a>
                <div class="media ">
                  <i class="ti-key icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                  <div class="media-body">
                    <p class="card-text">Add Users to your Properties..</p>
                  </div>
                </div>
            </div>
            <div class="col-md-6" style="padding-bottom:30px;">
                <a class="" href="{{ url('/amenity') }}"><h4>Amenities</h4></a>
                <div class="media">
                  <i class="ti-wheelchair icon-md text-info d-flex align-self-start me-3 text-warning"></i>
                  <div class="media-body">
                    <p class="card-text">Add Amenities to your Properties..</p>
                  </div>
                </div>
            </div>

            

            
          </div>
          


</div>

@endsection
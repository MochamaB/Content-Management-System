<!-- partial:partials/_sidebar.html -->


<nav class="sidebar sidebar-offcanvas">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/dashboard') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <!-----     -------------------->
    <!-----     -------------------->
    @if( Auth::user()->can('property.index') || Auth::user()->id === 1)
        <li class="nav-item nav-category">PROPERTIES</li>
    
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#properties" aria-expanded="false" aria-controls="properties">
            <i class="menu-icon mdi mdi mdi-bank"></i>
            <span class="menu-title">Properties</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="properties">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{ url('/property') }}">Properties</a></li>
              @if($routeParts[0] === 'property' && $routeParts[1] === 'show')
              <!-- Show link for the show method -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/property/' . $urlParts[5]) }}">Show</a></li>
              @elseif($routeParts[0] === 'property') <!-- Condition to show the Add Property menu -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/property/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif
              <li class="nav-item"> <a class="nav-link" href="{{ url('/unit') }}">Units</a></li>
              @if($routeParts[0] === 'unit' && $routeParts[1] === 'show')
              <!-- Show link for the show method -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/unit/' . $urlParts[5]) }}">Show</a></li>
              @elseif($routeParts[0] === 'unit') <!-- Condition to show the Add Property menu -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/unit/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif

            </ul>
          </div>
        </li>
    @endif
    <!-----     -------------------->
        <!-----     -------------------->
        <li class="nav-item nav-category">LEASING</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#leasing" aria-expanded="false" aria-controls="leasing">
            <i class="menu-icon mdi mdi-key"></i>
            <span class="menu-title">Leasing</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="leasing">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{ url('/lease') }}">Rent Roll</a></li>
              @if($routeParts[0] === 'lease' && $routeParts[1] === 'show')
              <!-- Show link for the show method -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/lease/' . $urlParts[5]) }}">Show</a></li>
              @elseif($routeParts[0] === 'lease') <!-- Condition to show the Add chartofaccounts menu -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/lease/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif
        

            </ul>
          </div>
        </li>
        
        <!-----     -------------------->
        <!-----     -------------------->

        <!-----     -------------------->
        <!-----     -------------------->
        <li class="nav-item nav-category">ACCOUNTING</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#accounting" aria-expanded="false" aria-controls="accounting">
            <i class="menu-icon mdi mdi-cash-usd"></i>
            <span class="menu-title">Accounting</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="accounting">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{ url('/chartofaccounts') }}">Chart of Accounts</a></li>
              @if($routeParts[0] === 'chartofaccounts' && $routeParts[1] === 'show')
              <!-- Show link for the show method -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/chartofaccounts/' . $urlParts[5]) }}">Show</a></li>
              @elseif($routeParts[0] === 'property') <!-- Condition to show the Add chartofaccounts menu -->
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/chartofaccounts/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif
        

            </ul>
          </div>
        </li>
        
        <!-----     -------------------->
        <!-----     -------------------->

        <!-----     -------------------->
        <!-----     -------------------->
        <li class="nav-item nav-category">COMMUNICATION</li>
        
        <!-----     -------------------->
        <!-----     -------------------->
        <li class="nav-item nav-category">SETTINGS</li>

        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="settings">
            <i class="menu-icon mdi mdi mdi-settings"></i>
            <span class="menu-title">Settings</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="settings">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{ url('/setting') }}">All Settings</a></li>

              @if(Request::url() === url('/settingsite')) <!-- Condition to show the Settings menu -->
              <li class="nav-item"> <a class="nav-link" href="{{ url('/settingsite') }}">Web Site Settings</a></li>
              @endif
              <!-- Condition to show the Add Slider menu -->
              @if(($routeParts[0] === 'slider')) 
              <li class="nav-item"><a class="nav-link" href="{{ url('/slider') }}">Slider</a></li>
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/slider/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif
              <!-- Condition to show the Amenity menu -->
              @if(($routeParts[0] === 'amenity')) 
              <li class="nav-item"><a class="nav-link" href="{{ url('/amenity') }}">Amenity</a></li>
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/amenity/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif
              <!-- Condition to show the Testimonial menu -->
              @if(($routeParts[0] === 'testimonial')) 
              <li class="nav-item"><a class="nav-link" href="{{ url('/testimonial') }}">Testimonial</a></li>
              <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/testimonial/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
              @endif

            </ul>
          </div>
        </li>
        <!------------------- ---------------------->
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                  <i class="menu-icon mdi mdi-account-circle-outline"></i>
                  <span class="menu-title">User Controls</span>
                  <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="auth">
                  <ul class="nav flex-column sub-menu">
                  @if( Auth::user()->can('user.index') || Auth::user()->id === 1)
                    <li class="nav-item"> <a class="nav-link" href="{{ url('/user') }}">All Users </a></li>
                    @if(($routeParts[0] === 'user')) 
                    <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/user/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
                    @endif
                  @endif
                  @if( Auth::user()->can('role.index') || Auth::user()->id === 1)
                    <li class="nav-item"> <a class="nav-link" href="{{ url('/role') }}">Roles </a></li>
                    @if(($routeParts[0] === 'role')) 
                    <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/role/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
                    @endif
                  @endif
                  @if( Auth::user()->can('permission.index') || Auth::user()->id === 1)
                    <li class="nav-item"> <a class="nav-link" href="{{ url('/permission') }}">Permissions </a></li>
                    @if(($routeParts[0] === 'permission')) 
                    <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/permission/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
                    @endif
                  @endif
                  </ul>
                </div>
              </li>
          <!---------- ----------------------------------------- -->


  </ul>
</nav>
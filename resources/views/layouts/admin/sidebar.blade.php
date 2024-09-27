<nav class="sidebar sidebar-offcanvas">

  
  <ul class="nav">
  <li class="nav-item nav-category" style="border-top: 1px solid rgba(255, 255, 255, 0.13)"></li>
  <li class="nav-item">
      <a class="nav-link" href="{{ url('/dashboard') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title" style="text-transform: uppercase;">Dashboard</span>
      </a>
    </li>
    <li class="nav-item nav-category" style="border-top: 1px solid rgba(255, 255, 255, 0.13)">Main Menu</li>

    <!-----     -------------------->
    <!-----     -------------------->
    @foreach ($sidebar['Main'] as $module => $moduleData)
    @php
    // Check if the user has permission for this module
    $hasPermission = $userPermissions->contains('module', $module);
    @endphp
    @if ($hasPermission || Auth::user()->id === 1)

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#{{$module}}" aria-expanded="false" aria-controls="{{$module}}">
        <i class="menu-icon mdi mdi mdi-{{$moduleData['icon']}}"></i>
        <span class="menu-title" style="text-transform: uppercase;">
          @if($module === 'User')
          Users
          @else
          {{$module}}
          @endif
        </span>
        <i class="menu-arrow"></i>
      </a>

      <div class="collapse" id="{{$module}}">
        <ul class="nav flex-column sub-menu">
          @foreach($moduleData['submodules'] as $submodule => $submoduleDisplay)
          @if( Auth::user()->can($submodule.'.index') || Auth::user()->id === 1)
          <li class="nav-item">
            @if($routeParts[0] === $submodule)
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @else
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @endif
          </li>
          @endif
          @endforeach

        </ul>
      </div>
    </li>
    @endif
    @endforeach


    <li class="nav-item nav-category" style="border-top: 1px solid rgba(255, 255, 255, 0.13)">Settings Menu</li>
    <!-----     -------------------->
    @foreach ($sidebar['Secondary'] as $module => $moduleData)
    @php
    // Check if the user has permission for this module
    $hasPermission = $userPermissions->contains('module', $module);
    @endphp
    @if ($hasPermission || Auth::user()->id === 1)

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#{{$module}}" aria-expanded="false" aria-controls="{{$module}}">
        <i class="menu-icon mdi mdi mdi-{{$moduleData['icon']}}"></i>
        <span class="menu-title" style="text-transform: uppercase;">
          @if($module === 'User')
          Users
          @else
          {{$module}}
          @endif
        </span>
        <i class="menu-arrow"></i>
      </a>

      <div class="collapse" id="{{$module}}">
        <ul class="nav flex-column sub-menu">
          @foreach($moduleData['submodules'] as $submodule => $submoduleDisplay)
          @if( Auth::user()->can($submodule.'.index') || Auth::user()->id === 1)
          <li class="nav-item">
            @if($routeParts[0] === $submodule)
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @else
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @endif
          </li>
          @endif
          @endforeach

        </ul>
      </div>
    </li>
    @endif
    @endforeach

    <li class="nav-item nav-category" style="border-top: 1px solid rgba(255, 255, 255, 0.13)">User Menu</li>
    @foreach ($sidebar['Other'] as $module => $moduleData)
    @php
    // Check if the user has permission for this module
    $hasPermission = $userPermissions->contains('module', $module);
    @endphp
    @if ($hasPermission || Auth::user()->id === 1)

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#{{$module}}" aria-expanded="false" aria-controls="{{$module}}">
        <i class="menu-icon mdi mdi mdi-{{$moduleData['icon']}}"></i>
        <span class="menu-title" style="text-transform: uppercase;">
          @if($module === 'User')
          Users
          @else
          {{$module}}
          @endif
        </span>
        <i class="menu-arrow"></i>
      </a>

      <div class="collapse" id="{{$module}}">
        <ul class="nav flex-column sub-menu">
          @foreach($moduleData['submodules'] as $submodule => $submoduleDisplay)
          @if( Auth::user()->can($submodule.'.index') || Auth::user()->id === 1)
          <li class="nav-item">
            @if($routeParts[0] === $submodule)
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @else
            <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}"> {{ $submoduleDisplay['display'] ?? $submodule }}</a>
            @endif
          </li>
          @endif
          @endforeach

        </ul>
      </div>
    </li>
    @endif
    @endforeach

    <li class="nav-item">
      <a class="nav-link" href="{{ url('/user/'.Auth::user()->id) }}">
        <i class="menu-icon mdi mdi-account-card-details "></i>
        <span class="menu-title" style="text-transform: uppercase;">PROFILE</span>
      </a>
    </li>
    @if( Auth::user()->can('account.index') || Auth::user()->id === 1)
    <li class="nav-item nav-category" style="border-top: 1px solid rgba(255, 255, 255, 0.13)">ACCOUNT</li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/account') }}">
      <span class="company-icon">
      {{ $sitesettings->initials }}
    </span>
        <span class="menu-title" style="text-transform: uppercase;"> {{ $sitesettings->company_name }}</span>
      </a>
    </li>
    @endif

  </ul>
</nav>
<script>
  $(document).ready(function() {
    $('.reload-link').click(function(event) {
      event.preventDefault(); // Prevent the default behavior (navigating to the href)
      location.reload(); // Reload the current page
    });
  });
</script>
<script>
  $(document).ready(function() {
    // Find the active tab and scroll to it
    var activeTab = $('.nav-link.active');

    if (activeTab.length > 0) {
      // Animate scrolling to the active tab
      $('html, body').animate({
        scrollTop: activeTab.offset().top - 500 // Adjust the offset as needed
      }, 500); // You can adjust the animation speed (in milliseconds)
    }
  });
</script>
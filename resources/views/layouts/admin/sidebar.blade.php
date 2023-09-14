<nav class="sidebar sidebar-offcanvas">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/dashboard') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title" style="text-transform: uppercase;">Dashboard</span>
      </a>
    </li>
    <!-----     -------------------->
    <!-----     -------------------->
    @foreach ($sidebar as $module => $moduleData)
    @php
    // Check if the user has permission for this module
    $hasPermission = $userPermissions->contains('module', $module);
    @endphp
    @if ($hasPermission || Auth::user()->id === 1)
          <li class="nav-item nav-category"></li>
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
                @foreach($moduleData['submodules'] as $submodule)
                @if( Auth::user()->can($submodule.'.index') || Auth::user()->id === 1)
                <li class="nav-item"> <a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule) }}">{{ $submodule }}</a></li>
                @if($routeParts[0] === $submodule && $routeParts[1] === 'show')
                <!-- Show link for the show method -->
                <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule.'/'. $urlParts[5]) }}">Show</a></li>
                @elseif($routeParts[0] === $submodule) <!-- Condition to show the Add Property menu -->
                <li class="nav-item"><a class="nav-link" style="text-transform: capitalize;" href="{{ url('/'.$submodule.'/'.$routeParts[1]) }}">{{$routeParts[1]}}</a></li>
                @endif
                @endif
                @endforeach

              </ul>
            </div>
          </li>
    @endif
    @endforeach

  </ul>
</nav>
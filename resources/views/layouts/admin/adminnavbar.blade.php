<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="">
        <img src="{{url('uploads/realsyslogosmall.png')}}" alt="No Image" style="height: 90%; width: 100%;">
      </a>
      <a class="navbar-brand brand-logo-mini" href="">
      <img src="{{url('uploads/images/Bridgetechlogo2.png')}}" alt="No Image" style="height: 90%; width: 70%;">
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex ">

    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h3 class="welcome-sub-text"><span class="text-black fw-bold">{{$sitesettings->site_name ?? 'Site Name'}}</span> System</h3>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item" >
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
          <span class="mdi mdi-menu" style="padding-right: 15px;"></span>
        </button>
      </li>
      

      @include('layouts.admin.notification')

      @php
      $user = Auth::user();
      $avatarUrl = $user ? $user->getFirstMediaUrl('avatar') : null;
      $avatarUrl = empty($avatarUrl) ? 'uploads/images/avatar.png' : $avatarUrl;
      @endphp
      <li class="nav-item dropdown  user-dropdown d-none d-lg-block">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="{{  url($avatarUrl) }}" alt="Profile image"> 
          <i class="icon-arrow-down ms-1" style="font-size:15px;font-weight:600"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-thumbnail rounded-circle" src="{{  url($avatarUrl) }}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->firstname ?? '' }} {{ Auth::user()->lastname ?? '' }}</p>
            <p class="fw-light text-muted mb-0">{{ Auth::user()->email ?? '' }}</p>
          </div>
          <a class="dropdown-item" href="{{ url('/user/'.Auth::user()->id) }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile </a>
          <a class="dropdown-item" href="{{ url('notification') }}"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages <span class="badge badge-pill badge-danger">1</span></a>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Logout</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        
      </li>

    </ul>
  </div>
</nav>
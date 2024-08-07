<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="">
        @if ($sitesettings)
        <img src="{{  $sitesettings->getFirstMediaUrl('logo') }}" alt="Logo" style="height: 70px; width: 150px;">
        @else
        <img src="{{url('uploads/images/logo7Copy.png')}}" alt="No Image" style="height: 70px; width: 150px;">
        @endif
      </a>
      <a class="navbar-brand brand-logo-mini" href="index.html">
        @if ($sitesettings)
        <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 70px; width: 150px;">
        @else
        <img src="{{url('uploads/images/logo7Copy.png')}}" alt="No Image">
        @endif
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex ">

    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h2 class="">Hi, <span class="text-black fw-bold">{{$user->firstname ?? 'Firstname'}} {{$user->lastname ?? 'lastname'}}</span></h2>
        <h4 class="welcome-sub-text">{{$sitesettings->site_name ?? 'Site Name'}} Dashboard</h4>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </li>
      <li class="nav-item d-none d-lg-block">
        <form class="search-form" action="#">
          <i class="icon-search"></i>
          <input type="search" class="form-control" placeholder="Search Here" title="Search here">
        </form>
      </li>

      @include('layouts.admin.notification')

      @php
      $user = Auth::user();
      $avatarUrl = $user ? $user->getFirstMediaUrl('avatar') : null;
      $avatarUrl = empty($avatarUrl) ? 'uploads/images/avatar.png' : $avatarUrl;
      @endphp
      <li class="nav-item dropdown  user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">

          <img class="img-xs rounded-circle" src="{{  url($avatarUrl) }}" alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-thumbnail rounded-circle" src="{{  url($avatarUrl) }}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->firstname ?? '' }} {{ Auth::user()->lastname ?? '' }}</p>
            <p class="fw-light text-muted mb-0">{{ Auth::user()->email ?? '' }}</p>
          </div>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
          <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Logout</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>

    </ul>
  </div>
</nav>
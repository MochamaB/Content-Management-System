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
        <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 70px; width: 150px;">
        @else
        <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
        @endif
      </a>
      <a class="navbar-brand brand-logo-mini" href="index.html">
        <img src="{{ url('resources/uploads/images/'. ($sitesettings->company_flavicon ?? 'noimage.jpg')) }}" style="height: 60px; width: 150px;">
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex ">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text">Hi, <span class="text-black fw-bold">{{$user->firstname ?? 'Firstname'}} {{$user->lastname ?? 'lastname'}}</span></h1>
        <h3 class="welcome-sub-text">{{$sitesettings->site_name ?? 'Site Name'}} Dashboard</h3>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown d-none d-lg-block">
        <a class="nav-link dropdown-bordered dropdown-toggle dropdown-toggle-split" id="messageDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false"> Select Category </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="messageDropdown">
          <a class="dropdown-item py-3">
            <p class="mb-0 font-weight-medium float-left">Select category</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Bootstrap Bundle </p>
              <p class="fw-light small-text mb-0">This is a Bundle featuring 16 unique dashboards</p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Angular Bundle</p>
              <p class="fw-light small-text mb-0">Everything you’ll ever need for your Angular projects</p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">VUE Bundle</p>
              <p class="fw-light small-text mb-0">Bundle of 6 Premium Vue Admin Dashboard</p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">React Bundle</p>
              <p class="fw-light small-text mb-0">Bundle of 8 Premium React Admin Dashboard</p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item d-none d-lg-block">
        <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
          <span class="input-group-addon input-group-prepend border-right">
            <span class="icon-calendar input-group-text calendar-icon"></span>
          </span>
          <input type="text" class="form-control">
        </div>
      </li>
      <li class="nav-item">
        <form class="search-form" action="#">
          <i class="icon-search"></i>
          <input type="search" class="form-control" placeholder="Search Here" title="Search here">
        </form>
      </li>
      @include('layouts.admin.notification')
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="icon-bell"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
          <a class="dropdown-item py-3">
            <p class="mb-0 font-weight-medium float-left">You have 7 unread mails </p>
            <span class="badge badge-pill badge-primary float-right">View all</span>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Marian Garner </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">David Grey </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
            </div>
            <div class="preview-item-content flex-grow py-2">
              <p class="preview-subject ellipsis font-weight-medium text-dark">Travis Jenkins </p>
              <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="{{ url('resources/uploads/images/'.Auth::user()->profilepicture ?? 'noimage.jpg') }}" alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-thumbnail rounded-circle" src="{{ url('resources/uploads/images/'.Auth::user()->profilepicture ?? 'noimage.jpg') }}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
            <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
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
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
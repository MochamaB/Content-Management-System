<li class="nav-item dropdown">
  
      @if($unreadNotifications->count() > 0)
      <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-bell"></i>
        <span class="badge badge-information">{{ $unreadNotifications->count() }}</span>
      </a>
      @else
      <!-- Display something when there are no unread notifications -->
      <a class="nav-link" href="#">
        <i class="icon-bell"></i>
        <span class="badge badge-information">0</span>
      </a>
      @endif
      <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
        <a class="dropdown-item py-3">
          <p class="mb-0 font-weight-medium float-left">You have {{ $unreadNotifications->count() }} unread mails </p>
          <span class="badge badge-pill badge-primary float-right">View all</span>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item preview-item">
          <div class="preview-item-content flex-grow py-2">
            <p class="preview-subject ellipsis font-weight-medium text-dark">Subject </p>
            <p class="fw-light small-text mb-0"> Heading </p>
          </div>
        </a>


      </div>
</li>
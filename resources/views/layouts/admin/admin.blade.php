<!DOCTYPE html>
<html lang="en">

@include('layouts.admin.adminheader')
<body>
<div id="progress-container">
     <div id="progress-bar"></div>
 </div>
  <!---- Page Loader ------------->
    <div id="loading-overlay" style="display:none;">
        <div class="loader"></div>
    </div>


  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->
    @include('layouts.admin.adminnavbar')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border me-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border me-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>
      <!---- -- -->
    
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      @include('layouts.admin.sidebar')
      <!-- partial -->
      <div class="main-panel" style="background-color: #F4F5F7;padding-top:0px">
        <div class="content-wrapper" style="background-color: #F4F5F7;padding-top:0px">
              <div class="home-tab">
                  <!-- Breadcrumb -->
                  @include('layouts.admin.breadcrumbs')
                        <!-- Breadcrumb -->
                        @yield('content')
              </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Developed BY <a href="https://www.bridgetechnologies.co.ke/" target="_blank">Bridge Technologies</a> Kenya.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2021. All rights reserved.</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  @include('layouts.admin.adminfooter')
  <!-- End custom js for this page-->
  
</body>

</html>


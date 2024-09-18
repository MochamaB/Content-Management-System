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


  <div class="container-scroller" style="min-height: 100vh;">
    @include('layouts.admin.admintheme')
    <!-- partial:partials/_navbar.html -->
    @include('layouts.admin.adminnavbar')
      <!-- partial:partials/_sidebar.html -->
      @include('layouts.admin.sidebar')
      <!-- partial -->
      <div class="main-panel" style="background-color: #F4F5F7;">
        <div class="content-wrapper" style="background-color: #F4F5F7;padding-top:70px">
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
    
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  @include('layouts.admin.adminfooter')
  <!-- End custom js for this page-->

</body>

</html>
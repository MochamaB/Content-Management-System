<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{$sitesettings->site_name ?? 'SITE NAME'}} </title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/typicons/typicons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/css/vendor.bundle.base.css') }}">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

  <!-- endinject -->
  <!-- Plugin css for this page -->


  <!--- Plugins for bootstrap table--------->
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.css') }}">
  <script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
  <script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js') }}"></script>
  <script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.css') }}"></script>
  <script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/libs/jsPDF/jspdf.umd.min.js"></script>
  <script src="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js') }}"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/style.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/mystyle.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/customstyle.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/wizard.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/notification.css') }}">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
  <!-- FOR THE PROGRESS BAR  -->


  <style>
    #progress-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      z-index: 9999;
      overflow: hidden;
      background-color: transparent;
    }

    #progress-bar {
      width: 0%;
      height: 100%;
      background-color: #ffaf00;
      /* Yellow color */
      transition: width 0.3s ease-in-out;
    }


    #loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      z-index: 9999;
    }

    .loader {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 120px;
      height: 120px;
      animation: spin 2s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>

</head>

  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/typicons/typicons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/css/vendor.bundle.base.css') }}">
  

  <!-- Bootstrap Table CSS -->
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.css') }}">

  <!-- Date Range CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
 
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/style.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/mystyle.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/wizard.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/css/vertical-layout-light/notification.css') }}">
  <link rel="stylesheet" href="{{ asset('styles/admin/vendors/select2/select2.min.css') }}">

  <link rel="shortcut icon" href="images/favicon.png" />

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Dropzone CSS -->
<link rel="stylesheet" href="{{ asset('styles/admin/vendors/dropzone/dropzone.css') }}">


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


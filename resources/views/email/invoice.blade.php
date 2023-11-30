<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{$sitesettings->site_name ?? 'SITE NAME'}} </title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/typicons/typicons.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/vendors/css/vendor.bundle.base.css') }}">

  <!--- Plugins for bootstrap table--------->
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.css') }}">
  <script src="{{ asset('resources/styles/admin/css/vertical-layout-light/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/style.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/mystyle.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/wizard.css') }}">
  <!-- endinject -->
  <style>
    #loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
 

    /* Safari */
    @-webkit-keyframes spin {
      0% {
        -webkit-transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(360deg);
      }
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
<div class="row">
    <div class="col-md-9">
        <div class=" contwrapper">
           
            <hr>

            <div class="col-md-12" style="text-align:center;">
                <h6>Terms & Condition</h6>
                <p>Refer to the terms and conditions on Lease agreement.</p>
                <p><a href="www.bridgetech.co.ke">POWERED BY BRIDGE PROPERTIES</a></p>
            </div>

        </div>
    </div>
    

</div>
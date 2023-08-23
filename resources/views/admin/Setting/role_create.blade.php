@extends('layouts.admin.admin')

@section('content')

<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'London')" id="defaultOpen">Summary</button>
  <button class="tablinks" onclick="openCity(event, 'Paris')" id="tabParis">Menu Access</button>
  <button class="tablinks" onclick="openCity(event, 'Tokyo')" id="tabTokyo">Report Access</button>
</div>
<form method="POST" action="{{ url($routeParts[0]) }}" id="myForm" enctype="multipart/form-data" novalidate>
  @csrf

  <div id="London" class="tabcontent">

    <h4>User Role Summary</h4>
    <hr>
    <div class="col-md-6">
      <div class="form-group">
        <label class="label">Role Name<span class="requiredlabel">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label class="label">Role Description<span class="requiredlabel">*</span></label>
        <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="description" name="description" required>
                                          </textarea>
      </div>
    </div>
    <div class="col-md-4">
      <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn1">Next: Menu Access</button>
    </div>
  </div>

  <div id="Paris" class="tabcontent">
    <h4>Menu/Module Access</h4>
    <hr>
    <!-- Menu Access content -->
    <div id="accordion">
      @foreach ($groupedPermissions as $module => $modulePermissions)
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{$module}}">
          <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $module}}Collapse" aria-expanded="true" aria-controls="collapseOne">
            <i class="menu-icon mdi mdi-plus"></i>
          </button>
          <h4>{{ $module}}</h4>
          <div class="form-check form-check-inline ">
            <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
            <label class="checkboxlabel pt-0 m-0" for="">
              Select All
            </label>
          </div>

        
        </div>

        <div id="{{ $module}}Collapse" class="collapse hide" aria-labelledby="{{$module}}" data-parent="#accordion">
          <div class="card-body">
            @foreach($modulePermissions as $submoduleName => $submodulePermissions)
            <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="d-flex justify-content-between align-items-center">
              @foreach ($submodulePermissions as $permission)
              <div class="form-check form-check-inline align-items-center">
                <input class="form-check-input p-0 body-checkbox" type="checkbox" name="permission[{{$permission->name}}]" value="{{$permission->name}}" id="">
                <label class="checkboxlabelbody pt-0 m-0" for="">
                  {{ $permission->action }}
                </label>
              </div>
              @endforeach
              <div class="" style="width: 120px;">
                <span style="color: blue;text-transform:capitalize">{{$submoduleName}}</span>
              </div>
            </div>

            @endforeach

          </div>
        </div>
      </div>
      @endforeach
    </div><br /><br />

    <div class="col-md-5">
      <div class="row">
        <div class="col-md-6">
          <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0" id="prevBtn2">Previous: Role</button>
        </div>
        <div class="col-md-6">
          <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" id="nextBtn2">Next: Reports</button>
        </div>
      </div>
    </div>
  </div>

  <div id="Tokyo" class="tabcontent">
    <!-- Report Access content -->
    <!-- ... -->

    <div class="col-md-5">
      <div class="row">
        <div class="col-md-6">
          <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0" id="prevBtn3">Previous: Menu</button>
        </div>
        <div class="col-md-6">
          <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submitBtn">Create Role</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
  }


  // Get the element with id="defaultOpen" and click on it
  document.getElementById("defaultOpen").click();


  // When the "Next" button for London tab is clicked
  document.getElementById("nextBtn1").addEventListener("click", function() {
    // Check if the required fields are filled
    var requiredInputs = document.querySelectorAll('input[required], textarea[required]');

    // Check if any required fields are empty
    var hasEmptyField = false;
    requiredInputs.forEach(function(input) {
      if (input.value.trim() === "") {
        input.classList.add("is-invalid");

        // Create the div element with the error message
        var errorDiv = document.createElement('div');
        errorDiv.className = "invalid-feedback";
        errorDiv.style.color = "red";
        errorDiv.textContent = "This field is required.";

        // Append the error div after the input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);

        hasEmptyField = true;
      } else {
        input.classList.remove("is-invalid");

        // Remove the error div if it exists
        var existingErrorDiv = input.nextElementSibling;
        if (existingErrorDiv && existingErrorDiv.className === "invalid-feedback") {
          existingErrorDiv.remove();
        }
      }
    });

    if (hasEmptyField) {
      return; // Stop execution if there are empty required fields
    }


    openCity({
      currentTarget: document.getElementById("tabParis")
    }, 'Paris');

    // Add "active" class to the Paris tab link
    document.getElementById("tabParis").classList.add("active");
  });

  // When the "Previous" button for Paris tab is clicked
  document.getElementById("prevBtn2").addEventListener("click", function() {
    openCity({
      currentTarget: document.getElementById("defaultOpen")
    }, 'London');
  });

  // When the "Next" button for Paris tab is clicked
  document.getElementById("nextBtn2").addEventListener("click", function() {
    openCity({
      currentTarget: document.getElementById("tabTokyo")
    }, 'Tokyo');

    // Add "active" class to the Tokyo tab link
    document.getElementById("tabTokyo").classList.add("active");
  });

  // When the "Previous" button for Tokyo tab is clicked
  document.getElementById("prevBtn3").addEventListener("click", function() {
    openCity({
      currentTarget: document.getElementById("tabParis")
    }, 'Paris');
  });
</script>


<!-- jQuery script to handle checkbox behavior -->
<!-- Your HTML code here -->

<script>
  $(document).ready(function() {
    // Add a change event listener to the "Select All" checkbox in each collapsible
    $('.header-checkbox').on('change', function() {
      // Find the parent collapsible section
      var collapsible = $(this).closest('.card-header').siblings('.collapse');

      // Get the state of the "Select All" checkbox
      var isChecked = $(this).prop('checked');

      // Find and set the state of body checkboxes within the same collapsible
      collapsible.find('.body-checkbox').prop('checked', isChecked);
    });
  });
</script>














@endsection
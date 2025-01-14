@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-md-3 left-side">
        <ul class="progress-bar">
            @foreach($steps as $index => $title)

            <li class="{{ $index < $activetab ? 'completed' : '' }} {{ $index === $activetab ? 'active' : '' }}">{{ $title }}</li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-9 right-side">
        <a href="#" class="mb-0 me-0  float-end" data-toggle="modal" data-target="#closeWizardModal">
            <i class=" mdi mdi-close-circle-outline me-2 closebtn"></i>
        </a>
        @foreach($stepContents as $index => $content)
        <div class="main {{ $index === 0 ? 'active' : '' }}">
            {!! $content !!}
        </div>
        @endforeach


    </div>
</div>

<div class="modal fade" id="closeWizardModal" tabindex="-1" role="dialog" aria-labelledby="closeWizardModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:red;">
                <h5 class="modal-title" id="closeWizardModalLabel" style="color:white;">Close Wizard</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to Exit this Wizard. All Data will be lost?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger btn-lg text-danger mb-0 me-2" data-dismiss="modal">Cancel</button>
                <!----- Goes to Close wizard method in settingsController ------>
                <form id="closeWizardForm" action="" method="POST">
                    @csrf <!-- CSRF token for Laravel -->
                    <button type="submit" class="btn btn-danger btn-lg text-white mb-0 me-0">Close Wizard</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        const $tabs = $(".progress-bar li");
        const $tabContents = $(".right-side .main");
        const totalSteps = '{{ count($steps) }}'
        let currentTab = '{{ $activetab ?? 0 }}';
        // alert(currentTab);

        const showTab = (tabIndex) => {
            $tabs.removeClass("active").eq(tabIndex).addClass("active");
            $tabContents.removeClass("show active").eq(tabIndex).addClass("show active");
            // Toggle visibility of previous button based on currentTab value
            $(".wizardpreviousBtn").toggle(tabIndex > 0);
            // Change next button text to "Complete" when it's the last tab
            const $nextButton = $(".nextbutton");
            if (tabIndex >= $tabs.length - 1) {
                //   alert($tabs.length);
                $nextButton.html('Complete & Save');
            } else {
                $nextButton.html('Next Step <i class="mdi mdi-arrow-right-bold-circle ms-1"></i> ');
            }
        };


        // Show the first tab on page load
        showTab(currentTab);

        $(".wizardpreviousBtn").on("click", function() {

            currentTab--;
            if (currentTab < 0) {
                currentTab = 0;
            }
            showTab(currentTab);
            // Update the URL with the new active tab
            history.replaceState({}, document.title, "?active_tab=" + currentTab);
            // Reload the page
            window.location.reload();
        });

        // Show the first tab on page load
        showTab(currentTab);
    });
</script>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const cancelBtn = document.querySelector("[data-dismiss='modal']");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", function() {
            // alert('Cancel button clicked!');
            $('#closeWizardModal').modal('hide');
        });
    }
});
   document.addEventListener("DOMContentLoaded", function() {
    
    // Store the original link URL when triggering the modal
    let targetUrl = null;

    // Add event listener for all links
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function(e) {
            // Check if the current URL is `/lease/create`
            if (window.location.pathname === "/lease/create") {
                // Prevent navigation
                e.preventDefault();

                // Check if it's not the close modal button itself
                if (!link.hasAttribute("data-target")) {
                    // Set the target URL to navigate after confirmation
                    targetUrl = link.href;
                    // Update the form action dynamically
                    // Extract the last part of the URL
                    const parts = targetUrl.split("/").filter(Boolean);
                    const lastPart = parts.length > 0 ? parts[parts.length - 1] : "lease";
                    const form = document.querySelector("#closeWizardForm");
                    form.action = `/closewizard/${encodeURIComponent(lastPart)}`;

                    // Show the modal
                    $('#closeWizardModal').modal('show');
                }
            }
        });
    });

    // Add event listener for the close wizard button (optional for the form)
    const closeWizardButton = document.querySelector(".closebtn");
    if (closeWizardButton) {
        closeWizardButton.addEventListener("click", function() {
            const form = document.querySelector("#closeWizardForm");

            // If no targetUrl, set the action to default route '/lease'
            if (!targetUrl) {
                form.action = `/closewizard/lease`;
            }

            // Show the modal
            $('#closeWizardModal').modal('show');
        });
    }

    // Handle the cancel button behavior
    const cancelBtn = document.querySelector("[data-dismiss='modal']");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", function() {
            alert();
            // Just dismiss the modal when clicking cancel
            $('#closeWizardModal').modal('hide');
        });
    }
});

</script>


@endsection
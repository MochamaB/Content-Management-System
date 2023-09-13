<div class="row">
    <div class="col-3 tab" style="padding:0px;">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            @foreach($tabTitles as $index => $title)
            @php
            $isDisabled = ($routeParts[1] === 'create') ? 'disabled' : '';
            @endphp
            <button class="tablinks @if($loop->first) active @endif" id="v-pills-{{ $loop->iteration }}-tab" data-toggle="pill" href="#v-pills-{{ $loop->iteration }}" role="tab" aria-controls="v-pills-{{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" {{ $isDisabled }} >
                {{ $title }}
            </button>
            @endforeach
        </div>
    </div>
    <div class="col-9 tabcontent ">

        <div class="tab-content" id="v-pills-tabContent">
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-{{ $loop->iteration }}" role="tabpanel" aria-labelledby="v-pills-{{ $loop->iteration }}-tab">
                {!! $content !!}
            </div>
            @endforeach
        </div>
    </div>
</div>



<!-- jQuery script to handle checkbox behavior -->
<!-- Your HTML code here -->
<script>
    $(document).ready(function() {
        const $tabs = $("#v-pills-tab .tablinks");
        const $tabContents = $("#v-pills-tabContent .tab-pane");
        let currentTab = '{{ $activetab ?? 0 }}';
       // alert(currentTab);

        const showTab = (tabIndex) => {
            $tabs.removeClass("active").eq(tabIndex).addClass("active");
            $tabContents.removeClass("show active").eq(tabIndex).addClass("show active");
        };

        const validateTab = (tabIndex) => {
            const $currentTab = $tabContents.eq(tabIndex);
            const $requiredFields = $currentTab.find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').remove();
                    $field.after('<div class="invalid-feedback">This field is required.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').remove();
                }
            });

            return isValid;
        };

        $(".nextBtn").on("click", function() {
            if (!validateTab(currentTab)) {
                return;
            }
            var password = $("#password").val();
            var confirmPassword = $("#password_confirmation").val();

            // Check if the passwords match
            if (password !== confirmPassword) {
                // Display an error message or highlight the fields
                $("#password_confirmation").addClass('is-invalid');
                $("#password_confirmation").siblings('.invalid-feedback').remove();
                $("#password_confirmation").after('<div class="invalid-feedback">Passwords Dont match.</div>');
                return;
            }

            currentTab++;
            if (currentTab >= $tabs.length) {
                currentTab = $tabs.length - 1;
            }
            showTab(currentTab);
        });

        // Show the first tab on page load
        showTab(currentTab);

        $(".previousBtn").on("click", function() {
            currentTab--;
            if (currentTab < 0) {
                currentTab = 0;
            }
            showTab(currentTab);
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
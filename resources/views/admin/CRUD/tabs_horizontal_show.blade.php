<div class="tab-container">
<ul class="nav nav-tabs mb-0" id="fx1" role="tablist">
@foreach($tabTitles as $index => $title)
@php
    // Define status classes
    $statusClasses = [
        'Paid' => 'active',
        'Unpaid' => 'warning',
        'Over Due' => 'danger',
        'Partially Paid' => 'dark',
        'Over Paid' => 'light',
        'Active' => 'active',
        'Expired' => 'warning',
        'Terminated' => 'error',
        'Suspended' => 'dark',
    ];

    // Resolve the status class based on the title
    $textClass = $statusClasses[$title] ?? 'secondary';
    if ($loop->first) {
        // If active and no status matches, set text-primary
        if (!isset($statusClasses[$title])) {
            $textClass = 'primary';
        }
    }

   
@endphp
        <li class="nav-item " role="presentation">
        <a class="nav-link @if($loop->first) active @endif text-{{ $textClass }}" 
            id="fx1-tab-{{ $loop->iteration }}" 
            data-bs-toggle="tab" href="#fx1-tabs-{{ $loop->iteration }}" 
            role="tab" aria-controls="fx1-tabs-{{ $loop->iteration }}" 
            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
            data-tab="{{ $title }}"
            style="font-size:0.95rem;padding:0px 20px 14px 20px; text-transform:capitalize">

                {{ $title }} ({{ $tabCounts[$title] ?? 0 }})
            </a>
        </li>
    @endforeach
</ul>
</div> 


<div class="tab-content" id="fx1-content" style="padding-top:1.6rem;">
            <!----------- ------------------>
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" 
            id="fx1-tabs-{{ $loop->iteration }}" 
            role="tabpanel" 
            aria-labelledby="fx1-tab-{{ $loop->iteration }}">
            {!! $content !!}
        </div>

        <!----------- ------------------>  
        @endforeach
</div>
<script>
    $(document).ready(function() {
        // Function to update the URL with the tab name
        function updateUrl(tabName) {
            history.pushState(null, null, '?' + $.param({ tab: tabName }));
        }

        // Add a click event listener to each tab link
        $('#fx1 a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Get the tab name from the data attribute
            var tabName = $(e.target).data('tab');

            // Update the URL with the tab name
            updateUrl(tabName);
        });

        // Check if there is a tab parameter in the URL
        var urlParams = new URLSearchParams(window.location.search);
        var activeTab = urlParams.get('tab');

        // If no tab parameter is present, set the default tab dynamically
        if (!activeTab) {
            // Find the first tab link and get its data-tab attribute
            var defaultTab = $('#fx1 a[data-bs-toggle="tab"]:first').data('tab');

         //   alert(defaultTab);

            // Show the default tab
            $('#fx1 a[data-tab="' + defaultTab + '"]').tab('show');

            // Update the URL with the default tab name
            updateUrl(defaultTab);
        } else {
            // Activate the tab specified in the URL
            $('#ex1 a[data-tab="' + activeTab + '"]').tab('show');
        }
    });
</script>


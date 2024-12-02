@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-3 tab" style="padding:0px;">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            @foreach($tabTitles as $index => $title)
            @php
            $isDisabled = ($routeParts[1] === 'create') ? 'disabled' : '';
            @endphp
            <button class="tablinks @if($loop->first) active @endif" id="v-pills-{{ $loop->iteration }}-tab" data-toggle="pill" href="#v-pills-{{ $loop->iteration }}" role="tab" aria-controls="v-pills-{{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" >
            <i class="{{ $tabIcons[$title] }}" style="padding-right:10px;font-size:21px"></i>&nbsp; {{ $title }}
            </button>
            @endforeach
        </div>
    </div>
    <div class="col-9 tabcontent" style="padding:0px;border:none;background-color:#F4F5F7">

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
    $(document).ready(function () {
        // Default to the first tab on page load
        const defaultTab = $('.tablinks.active').text().trim();
        updateUrl(defaultTab);

        // Handle tab click
        $('.tablinks').on('click', function () {
            // Get the title of the clicked tab
            const activeTabTitle = $(this).text().trim();

            // Update the URL
            updateUrl(activeTabTitle);
        });

        // Function to update the URL with the tab title
        function updateUrl(tabTitle) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabTitle); // Set the 'tab' parameter
            window.history.replaceState(null, '', url); // Update the URL without reloading
        }

        // On page load, highlight the tab based on the URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab');
        if (tabFromUrl) {
            $('.tablinks').each(function () {
                if ($(this).text().trim() === tabFromUrl) {
                    $(this).trigger('click');
                }
            });
        }
    });
</script>

@endsection
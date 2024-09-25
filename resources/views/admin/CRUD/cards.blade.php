<style>
    .statistics-details {
        display: flex;
        /* Use Flexbox for layout */
        flex-wrap: wrap;
        /* Allow items to wrap if necessary */
    }

    .statistics-details .col {
        display: flex;
        /* Use Flexbox within each column */
        flex-direction: column;
        /* Stack content vertically */
        justify-content: space-between;
        /* Distribute content evenly */
        box-sizing: border-box;
        /* Ensure padding and border are included in width/height */
        min-height: 50px;
        /* Set a minimum height for cards */
    }

    .row .col:not(:first-child) {
        border-left: 2px solid #F4F5F7;
    }

    /* Screen size under 576px (phone) */
    @media (max-width: 576px) {
        .row .col:not(:first-child) {
            border-left: none;
        }

        .row .col:last-child,
        .row .col:nth-last-child(2) {
            border-bottom: none;
            /* Remove border-bottom for last and second-to-last children */
        }
    }
</style>
<div class="row statistics-details " style="margin: 10px 0px; padding:15px 10px 0px 0px; ">
    @foreach($cardData as $cardType => $card)
    <!-- TOTAL CARDS -->
    <div class="col text-left pb-2">
        @if (!empty($card['value']) || $card['value'] === 0)
        <p class="statistics-title d-flex align-items-bottom">
            <i class="mdi mdi-numeric" style="color: #1F3BB3; padding-left: 5px;"></i> <!-- Change the color and add padding -->
            <span style="padding-left: 5px;">{{ $card['title'] }}</span>
        </p> <!--Title -->

        <h3 class="rate-percentage text-center">
            {{ $card['value'] ?? 0 }}
        </h3>
        @elseif(!empty($card['amount']) || $card['amount'] === 0)
        <p class="statistics-title d-flex align-items-bottom">
            <i class="mdi mdi-cash" style="color: #5dc71b; padding-left: 5px;"></i> <!-- Change the color and add padding -->
            <span style="padding-left: 5px;">{{ $card['title'] }}</span>
        </p> <!--Title -->

        <h3 class="rate-percentage text-left d-flex justify-content-center align-items-center">
            <span class="text-muted font-weight-medium text-small d-flex align-items-center me-2">
                {{ $sitesettings->site_currency ?? '' }}
            </span>
            {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}
        </h3>

        @elseif(!empty($card['percentage']) || $card['percentage'] === 0)
        <p class="statistics-title d-flex align-items-bottom">
            <i class="mdi mdi-percent" style="color: #1F3BB3; padding-left: 5px;"></i>
            <!-- Change the color and add padding -->
            <span style="padding-left: 5px;">{{ $card['title'] }}</span>
        </p> <!--Title -->
        @php
            $percentage = $card['percentage'] ?? 0;
            // Determine the color based on percentage value
            if ($percentage < 50) {
                $color = 'red';
            } elseif ($percentage > 80) {
                $color = 'blue';
            } else {
                $color = 'black'; // Default color for percentages between 50 and 80
            }
        @endphp
        <h3 class="rate-percentage" style="color: {{ $color }};">

            {{ $card['percentage'] ?? 0 }} %
        </h3>
        @endif
        <!--link -->
        <!--- Footer -->
        @if (!empty($card['links']))
        <h6 class="text-muted text-center" >
                        <a class="text-muted text-small" href="{{ url($card['links']) }}">
                            View More</a>
                </h6>
        @endif

    </div>
    @endforeach

</div>
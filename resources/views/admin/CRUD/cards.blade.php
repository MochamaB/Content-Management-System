<style>
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
            <!-- Change the color and add padding -->
            <span style="padding-left: 5px;">{{ $card['title'] }}</span>
        </p> <!--Title -->
        <h3 class="rate-percentage">

            {{ $card['percentage'] ?? 0 }} %
        </h3>
        @endif
        <!--link -->
        <!--- Footer -->
        @if (!empty($card['links']))
        <p class="text-muted">
            {{ $card['links'] }}
        </p>
        @endif

    </div>
    @endforeach

</div>
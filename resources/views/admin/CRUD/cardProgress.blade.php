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
                    @if($cardType !== 'totalCount')
                    <!-- TOTAL CARDS -->
                    <div class="col text-left pb-2">
                        <p class="statistics-title d-flex align-items-bottom"><!-- Change the color according to class -->
                            {{ $card['title'] }}
                        </p> <!--Title -->
                        @if (!empty($card['value']) || $card['value'] === 0)
                        <h3 class="rate-percentage text-center text-{{ $card['class'] }}">
                            {{ $card['value'] ?? 0 }}
                        </h3>
                        @elseif(!empty($card['amount']) || $card['amount'] === 0)
                        <h3 class="rate-percentage text-left d-flex justify-content-center align-items-center">
                            <span class="text-muted font-weight-medium text-small d-flex align-items-center me-2">
                                {{ $sitesettings->site_currency ?? '' }}
                            </span>
                            {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}
                        </h3>
                        @endif
                        <!--link -->
                    </div>
                    @endif
                    @endforeach
                    <!--- PROGRESS ---------->
                    <div class="progress mt-3" style="padding:0px">
                        @foreach($cardData as $cardType => $card)
                        @if($cardType !== 'totalCount' && !empty($card['percentage']))
                        <div class="progress-bar bg-{{ $card['class'] }}"
                            role="progressbar"
                            style="border-radius:0px; width: {{ $card['percentage'] }}%"
                            aria-valuenow="{{ $card['percentage'] }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            data-placement="top"
                            data-toggle="tooltip"
                            title="{{ $card['tooltip'] }}">
                        </div>
                        @endif
                        @endforeach
                    </div>
                    <!--- LEGENDS -->
                    <div class="lenear-multiple-progress-legends d-flex justify-content-between pt-1">
                        <div>
                            <p class="statistics-title">{{ $cardData['totalCount']['title']}}</p>
                        </div>
                        <div>
                            <p class="statistics-title">{{ $cardData['totalCount']['total']}}</p>
                        </div>
                    </div>

                </div>

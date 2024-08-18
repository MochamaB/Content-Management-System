<div class="row statistics-details d-flex align-items-center justify-content-between">
    @foreach($propertyCard as $cardType => $card)
    <!-- TOTAL CARDS -->
    <div class="col text-center">
        <p class="statistics-title">{{ $card['title'] }}</p> <!--Title -->
        @if (!empty($card['value']))
        <h3 class="rate-percentage">
            {{ $card['value'] }}
        </h3>
        @elseif(!empty($card['amount']))
        <h3 class="rate-percentage">
            {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}
            <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}
            </span>
        </h3>
        @elseif(!empty($card['percentage']))
        <h3 class="rate-percentage">

            {{ $card['percentage'] }} %
        </h3>
        @endif
        <!--link -->
        @if (!empty($card['links']))
                 <p>
                        <a class="text-primary text-small" href="{{ url($card['links']) }}">
                            View More</a>
                </p>
                 @endif
    </div>
    @endforeach

</div>
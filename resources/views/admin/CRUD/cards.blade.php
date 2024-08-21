<div class="row statistics-details d-flex align-items-center justify-content-between mb-3">
    @foreach($cardData as $cardType => $card)
    <!-- TOTAL CARDS -->
    <div class="col text-center" style="border-left:2px solid #F4F5F7 ">
        <p class="statistics-title">{{ $card['title'] }}</p> <!--Title -->
        @if (!empty($card['value']))
        <h3 class="rate-percentage">
            {{ $card['value'] ?? 0 }}
        </h3>
        @elseif(!empty($card['amount']))
        <h3 class="rate-percentage text-center d-flex">
            {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}
            <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                &nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}
            </span>
        </h3>
        @elseif(!empty($card['percentage']))
        <h3 class="rate-percentage">

            {{ $card['percentage'] ?? 0 }} %
        </h3>
        @endif
        <!--link -->
      
    </div>
    @endforeach

</div>
<style>
    .row .col:not(:first-child) {
    border-left: 2px solid #F4F5F7;
}
</style>
<div class="row statistics-details " style="margin: 10px; padding:10px;" >
    @foreach($cardData as $cardType => $card)
    <!-- TOTAL CARDS -->
    <div class="col text-center">
        <p class="statistics-title">{{ $card['title'] }}</p> <!--Title -->
        @if (!empty($card['value']) || $card['value'] === 0)
        <h3 class="rate-percentage">
            {{ $card['value'] ?? 0 }}
        </h3>
        @elseif(!empty($card['amount']) || $card['amount'] === 0)
        <h3 class="rate-percentage text-center d-flex justify-content-center align-items-center">
        <span class="text-muted font-weight-medium text-small d-flex align-items-center me-2">
            {{ $sitesettings->site_currency ?? '' }}
        </span>
        {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}
        </h3>

        @elseif(!empty($card['percentage']) || $card['percentage'] === 0)
        <h3 class="rate-percentage">

            {{ $card['percentage'] ?? 0 }} %
        </h3>
        @endif
        <!--link -->
      
    </div>
    @endforeach

</div>
<hr>
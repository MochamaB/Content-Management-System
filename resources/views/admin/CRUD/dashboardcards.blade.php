<div class="row" style="margin-top: 0px;">

    @foreach($cardData as $cardType => $card)
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#ffff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-pin icon-md"></i>
                        &nbsp; {{ $card['title'] }}
                    </span>
                </p>
            </div>
            <div class="card-body" style="padding-top:5px;padding-bottom: 0.7em;">
                @if (!empty($card['value']))
                <h2 class="rate-percentage text-primary d-flex justify-content-between">
                    {{ $card['value'] }}

                </h2>
                @else
                <h2 class="rate-percentage text-success d-flex">
                    {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}

                    <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}</span>
                </h2>
                @endif
            </div>
            @if (!empty($card['links']))
            <div class="card-footer" style="background-color:#fff ;">
                <h6 class="text-muted">
                    .
                    <span class="text-warning font-weight-medium">
                        <a class="table" href="{{ url($card['links']) }}">
                            View More</a>
                    </span>
                </h6>
            </div>
            @endif
        </div>
    </div>

    @endforeach

</div>


<!-------        ----------->
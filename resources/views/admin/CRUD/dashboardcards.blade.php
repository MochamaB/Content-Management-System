<div class="row" style="margin-top: 0px;">

    @foreach($cardData as $cardType => $card)
    <div class="col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <!--- TITLE -->
                
             <!--- BODY -->
                @if (!empty($card['value']))
                <h4 class="card-title card-title-dash mt-1 d-flex align-items-bottom">
                    <i class="mdi mdi-numeric" style="color: #1F3BB3; padding-left: 5px;font-size:20px"></i> <!-- Change the color and add padding -->
                    <span style="padding-left: 5px;">{{ $card['title'] }}</span>
                </h4> <!--Title -->
                <h2 class="rate-percentage text-primary d-flex justify-content-between">
                    {{ $card['value'] }}
                </h2>
                @else
                <h4 class="card-title card-title-dash mt-1 d-flex align-items-bottom">
                    <i class="mdi mdi-cash" style="color: #5dc71b; padding-left: 5px;font-size:20px"></i> <!-- Change the color and add padding -->
                    <span style="padding-left: 5px;">{{ $card['title'] }}</span>
                </h4> <!--Title -->
                <h2 class="rate-percentage text-success d-flex">
                    {{ number_format(floatval($card['amount'] ?? 0), 0, '.', ',') }}

                    <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}</span>
                </h2>
                @endif
                 <!--- Footer -->
                 @if (!empty($card['links']))
                 <h6 class="text-muted">
                        <a class="text-muted text-small" href="{{ url($card['links']) }}">
                            View More</a>
                </h6>
                 @endif
            </div>
        </div>
    </div>
    @endforeach
</div>


<!-------        ----------->
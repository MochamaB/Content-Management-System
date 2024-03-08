<div class="row" style="margin-top: 0px;">

    @foreach($cardData as $cardType => $card)

    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#ffff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        
                        &nbsp; {{ $card['title'] }}
                    </span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                @if(isset($card['value']) || isset($card['amount']) || isset($card['percentage']))
                    @if(isset($card['value']))
                        <h2 class="rate-percentage text-primary d-flex justify-content-between">
                        {{ $card['value'] }}
                            
                        </h2>
                    @elseif(isset($card['amount']))
                        <h2 class="rate-percentage text-success d-flex">
                        $  {{ $card['amount'] }}
                        <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}</span>
                        </h2>
                    @elseif(isset($card['percentage']))
                    {{ $card['percentage'] }}
                    @else
                    Property not available
                    @endif
                @endif
            </div>
            <div class="card-footer" style="background-color:#fff ;">
                <h6 class="text-muted">
                    View More
                    <span class="text-warning font-weight-medium">
                        Text
                    </span>
                </h6>
            </div>
        </div>
    </div>
    @endforeach
<hr>
<div class="row" style="margin-top: 20px;">

    @foreach($cardData['cards'] as $card => $cardType)
    @if($cardType === 'information')
    <!-- Information Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium"> 
                    Total {{$card ?? 'Title'}}</p>
            </div>
            <div class="card-body" style="padding-top:7px;">
                <h2 class="rate-percentage text-primary d-flex justify-content-between">
                    {{ $cardData['data'][$card] }}
                    <span class="text-success text-small d-flex align-items-center">
                        <i class="mdi mdi-trending-up me-2 icon-md"></i>+20%</span>
                </h2>

            </div>
        </div>
    </div>
    @elseif($cardType === 'progress')
    <!-- Progress Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium"> Total {{$card ?? 'Title'}}</p>
            </div>
            <div class="card-body" style="padding-top:7px;">
                <h2 class="text-primary">
                    @if(is_array($cardData['data'][$card]))
                    {{ $cardData['data'][$card]['modeltwoCount'] }}
                    @else
                    {{ $cardData['data'][$card] }}
                    @endif
                </h2>
                <div class="d-flex justify-content-between">
                    <p class="text-muted">Avg: {{$card ?? 'Title'}}</p>
                    <p class="text-muted">max: @if(is_array($cardData['data'][$card]))
                        {{ $cardData['data'][$card]['modelCount'] }}
                        @else
                        {{ $cardData['data'][$card] }}
                        @endif
                    </p>
                </div>
                <div class="progress progress-md">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ is_array($cardData['data'][$card]) ? $cardData['data'][$card]['percentage'] : $cardData['data'][$card] }}%" aria-valuenow="{{ is_array($cardData['data'][$card]) ? $cardData['data'][$card]['percentage'] : $cardData['data'][$card] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
<hr>

<!-------        ----------->
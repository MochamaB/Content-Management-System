
<div class="row" style="margin-top: 0px;">

    @foreach($cardData['cards'] as $card => $cardType)
    @if($cardType === 'information')
    <!-- Information Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-pin icon-md"></i>
                        &nbsp;{{$card ?? 'Title'}}
                    </span>
                    <span class=" text-small d-flex align-items-center">
                        <i class="mdi mdi-arrow-right-bold me-2 mdi-24px "></i></span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                <h2 class="rate-percentage text-primary d-flex justify-content-between">
                    @if(is_array($cardData['data'][$card]))
                    {{ $cardData['data'][$card]['modelCount'] }}
                    @else
                    {{ $cardData['data'][$card] }}
                    @endif
                    <span class="text-success text-small d-flex align-items-center">
                        <i class="mdi mdi-trending-up me-2 icon-md"></i>+20%</span>
                </h2>

            </div>
            <div class="card-footer" style="background-color:#fff ;">
                <h6 class="text-muted">
                    @if(is_array($cardData['data'][$card]))
                    {{ $cardData['data'][$card]['informationCardInfo'] }}
                    @else
                    View More in Module
                    @endif
                    <span class="text-warning font-weight-medium">
                        @if(is_array($cardData['data'][$card]))
                        {{ $cardData['data'][$card]['modeltwoCount'] }}
                        @else
                        .
                        @endif
                    </span>
                </h6>
            </div>
        </div>
    </div>
    @elseif($cardType === 'detail')
    <!-- Details Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-numeric-0-box-multiple-outline icon-md"></i>
                        &nbsp;&nbsp;&nbsp;{{$card ?? 'Title'}}
                    </span>
                    <span class=" text-small d-flex align-items-center">
                        <i class="mdi mdi-arrow-right-bold me-2 mdi-24px "></i></span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                <h2 class="rate-percentage text-primary d-flex justify-content-between">
                    {{ $cardData['data'][$card] }}
                    <span class="text-success text-small d-flex align-items-center">
                        <i class="mdi mdi-trending-up me-2 icon-md"></i>+20%</span>
                </h2>
            </div>
            <div class="card-footer" style="background-color:#fff ;">
                <p class="text-muted">
                    <a class="table" href="">View More Information</a>
                </p>
            </div>
        </div>
    </div>

    @elseif($cardType === 'total')
    <!-- Details Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-numeric icon-md"></i>
                        &nbsp;&nbsp;&nbsp;{{$card ?? 'Title'}}
                    </span>
                    <span class=" text-small d-flex align-items-center">
                        <i class="mdi mdi-arrow-right-bold me-2 mdi-24px "></i></span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                <div class="d-flex justify-content-between">
                    <h2 class="text-primary">
                        @if(is_array($cardData['data'][$card]))
                        {{ $cardData['data'][$card]['modelCount'] }}
                        @endif
                    </h2>
                    <h2 class="text-warning">
                        @if(is_array($cardData['data'][$card]))
                        {{ $cardData['data'][$card]['modeltwoCount'] }}
                        @endif
                    </h2>
                </div>
                <div class="d-flex justify-content-between">
                    <p class="text-muted">Generated</p>
                    <p class="text-muted">Paid
                    </p>
                </div>
            </div>

        </div>
    </div>
    @elseif($cardType === 'progress')
    <!-- Progress Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-percent icon-md"></i>
                        &nbsp;{{$card ?? 'Title'}}
                    </span>
                    <span class=" text-small d-flex align-items-center">
                        <i class="mdi mdi-arrow-right-bold me-2 mdi-24px "></i></span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                <h2 class="text-primary">
                    @if(is_array($cardData['data'][$card]))
                    {{ $cardData['data'][$card]['modeltwoCount'] }}
                    @else
                    {{ $cardData['data'][$card] }}
                    @endif
                </h2>
                <div class="d-flex justify-content-between">
                    <p class="text-muted">{{$card ?? 'Title'}}</p>
                    <p class="text-muted">Total: @if(is_array($cardData['data'][$card]))
                        {{ $cardData['data'][$card]['modelCount'] }}
                        @else
                        {{ $cardData['data'][$card] }}
                        @endif
                    </p>
                </div>
                <div class="progress progress-md" style="height:10px">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ is_array($cardData['data'][$card]) ? $cardData['data'][$card]['percentage'] : $cardData['data'][$card] }}%" aria-valuenow="{{ is_array($cardData['data'][$card]) ? $cardData['data'][$card]['percentage'] : $cardData['data'][$card] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

            </div>
        </div>
    </div>
    @elseif($cardType === 'cash')
    <!-- Details Card -->
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header" style="background-color:#fff ;">
                <p class="card-title card-title-dash font-weight-medium d-flex justify-content-between">
                    <span class="d-flex align-items-center">
                        <i class="text-warning mdi mdi-cash-multiple icon-md"></i>
                        &nbsp;&nbsp;&nbsp;{{$card ?? 'Title'}}
                    </span>
                    <span class=" text-small d-flex align-items-center">
                        <i class="mdi mdi-arrow-right-bold me-2 mdi-24px "></i></span>
                </p>
            </div>
            <div class="card-body" style="padding-top:7px;padding-bottom: 0.1em;">
                <h2 class="rate-percentage text-success d-flex">
                    $ @if(is_array($cardData['data'][$card]))
                    {{ $cardData['data'][$card]['modelCount'] }}
                    @endif
                    <span class="text-muted font-weight-medium text-small d-flex align-items-center">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $sitesettings->site_currency ?? '' }}</span>
                </h2>
            </div>
            <div class="card-footer" style="background-color:#fff ;">
                <p class="text-muted">
                    <a class="table" href="">View More Information</a>
                </p>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>


<!-------        ----------->
<div class="d-sm-flex justify-content-between align-items-start">
    <div>
        <h5 class="card-title card-title-dash pt-1"><b>Summary Overview</b></h5>
    </div>
    @if(isset($filters))
    <div>
        @if(!empty($filters['from_date']) && !empty($filters['to_date']))

        <span class="badge badge-filter d-flex align-items-center">
            <i class="mdi mdi-calendar" style="font-size: 16px;vertical-align: middle; padding-left: 5px;"></i>
            <span style="vertical-align: middle; padding-left: 5px;">
                {{ $filters['from_date'] }}
                <span style="font-size: 17px; vertical-align: middle;"> / </span>
                {{ $filters['to_date'] }}
            </span>
        </span>


        @else
        <span class="badge badge-filter d-flex align-items-center">
            <i class="mdi mdi-calendar" style="font-size: 16px;vertical-align: middle; padding-left: 5px;"></i>
            <span style="vertical-align: middle; padding-left: 5px;">
                This month
            </span>
        </span>
        @endif
    </div>
    @endif
</div>
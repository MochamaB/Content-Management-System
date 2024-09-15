<div class="d-sm-flex justify-content-between align-items-start">
    <div>
        <h5 class="card-title card-title-dash pt-1"><b>Summary Overview</b></h5>
    </div>
    @if(isset($filters))
    <div>
      

        <span class="badge badge-filter d-flex align-items-center" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="mdi mdi-calendar" style="font-size: 16px;vertical-align: middle; padding-left: 5px;"></i>
                <span style="vertical-align: middle; padding-left: 5px;">
                @if(!empty($filters['from_date']) && !empty($filters['to_date']))
                    {{ \Carbon\Carbon::parse($filters['from_date'])->format('Y M') }}
                    <span style="font-size: 17px; vertical-align: middle;"> - </span>
                    {{ \Carbon\Carbon::parse($filters['to_date'])->format('Y M') }}
                @else
                        This Month
                @endif
                </span>
        </span>
    </div>
    @endif
</div>
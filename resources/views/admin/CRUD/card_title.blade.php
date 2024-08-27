@if(isset($filters))
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title card-title-dash pt-1"><b>Summary Overview</b></h5>
                </div>
                <div>
                    @if(!empty($filters['from_date']) && !empty($filters['to_date']))

                    <span class="badge badge-filter"> Date Range: {{ $filters['from_date'] }}  <span style="font-size:17px;">/</span>  {{ $filters['to_date'] }}</span>

                    @else
                    <span class="badge badge-filter"> This month </span>
                    @endif
                </div>
            </div>
        @endif
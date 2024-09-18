
<form id="dateRangeForm" action="{{ url($routeParts[0]) }}" method="GET" style="margin-bottom:0px" class="d-flex justify-content-end">
        <div class="form-group mr-4" style="margin-bottom:0px">
            <input type="text" class="form-control" id="daterange" name="daterange" style="max-width: 250px;" />
        </div>
        <input type="hidden" name="from_date" id="from_date">
        <input type="hidden" name="to_date" id="to_date">
    </form>
    
            <form action="{{  url($routeParts[0]) }}" method="GET" style="margin-bottom:0px" class="d-flex justify-content-end">
                <div class="form-group mr-4" style="margin-bottom:0px">
                <input type="date" class="form-control" id="" name="from_date" value="{{ request('from_date', now()->startOfMonth()->toDateString()) }}" style="max-width: 150px;" />
                </div>

                <div class="form-group mr-4" style="margin-bottom:0px">
                <input type="date" class="form-control" id="" name="to_date" value="{{ request('to_date', now()->toDateString())  }}" style="max-width: 150px;" />
                </div>

                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0">
                <i class="mdi mdi-filter mdi-24" style="font-size: 12px;color:white"></i>
                Filter</button>
            </form>
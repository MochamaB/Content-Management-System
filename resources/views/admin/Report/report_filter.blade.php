<div class=" contwrapper" style="border-left:5px solid #1F3BB3; margin-bottom:15px;padding-bottom:0px; background-color:#f8f9fa;">
<form method="GET" action="{{ url('report/'.$report->id) }}">    
<div class="row">

        @if (isset($filters))
        @foreach($filterdata as $key => $filter)
        <div class="col-md-3" style="padding:0px 5px 0px 5px;">
            <div class="form-group">
                <label class="label">{{ $filter['label'] }}</label>
                <select class="formcontrol2" name="{{ $key }}" id="">
                    <option value="All">All {{ $filter['label'] }}</option>
                    @foreach ($filter['values'] as $id => $value)
                    <option value="{{ $id }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endforeach
            <div class="col-md-3 ms-auto text-end" style="padding:35px 10px 0px 10px" >
                <button type="button" class="btn btn-warning btn-lg text-white mt-0 me-2 ">Export Report</button>
                <button type="submit" class="btn btn-primary btn-lg text-white mt-0 me-0 nextbutton">Run Report</button>
            </div>
        
  
        @else
        <div class="col-md-12">
            <h4>Filter not available.</h4>
        </div>
        @endif
    </div>
</form>

</div>
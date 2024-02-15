<div class=" contwrapper" style="border-left:5px solid #1F3BB3; margin-bottom:15px;padding-bottom:0px; background-color:#F4F5F7">
    <form method="GET" action="{{ url()->current() }}">
        <div class="row">

            @if (isset($filterdata))
            @foreach($filterdata as $key => $filter)
            <div class="col-md-3" style="padding:0px 5px 0px 5px;">
                <div class="form-group">
                    <label class="label">{{ $filter['label'] }}</label>

                    @if($filter['inputType'] == 'select')
                    <select class="formcontrol2" name="{{ $key }}" id="">
                        <option value="">All {{ $filter['label'] }}</option>
                        @foreach ($filter['values'] as $id => $value)
                        <option value="{{ $id }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @elseif($filter['inputType'] == 'selectdefault')
                    <select class="formcontrol2" name="{{ $key }}" id="">
                        @foreach ($filter['values'] as $id => $value)
                        <option value="{{ $id }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <!---- GROUP SELECT ------------->
                    @elseif($filter['inputType'] === 'selectgroup')
                    <select class="formcontrol2" id="{{ $key }}" name="{{ $key }}">
                        @foreach ($filter['values'] as $groupKey => $groupValues)
                        <optgroup label="{{ $groupKey }}">
                            @foreach ($groupValues as $id => $value)
                            <option value="{{ $id }}">{{ $value }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                    @endif

                </div>
            </div>
            @endforeach
            <div class="col-md-3 ms-auto text-end" style="padding:35px 10px 0px 10px">
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
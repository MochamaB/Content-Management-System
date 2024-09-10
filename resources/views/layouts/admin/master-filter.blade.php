@if (isset($filterdata))
@foreach($filterdata as $key => $filter)
@if ($filter['filtertype'] === 'main')
<div class="col-md-3">
    <div class="form-group">

        @if ($filter['inputType'] === 'select')
        <select name="{{ $key }}" id="controller" class="formcontrol2 mainfilter">
            <option value="">All {{ $filter['label'] }}</option>
            @foreach ($filter['values'] as $id => $value)
            <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @endif

    </div>

</div>
@endif
@endforeach
@else
@foreach ($defaultfilter as $key => $filter)
<div class="col-md-3">
    <div class="form-group">

        @if ($filter['inputType'] === 'select')
        <select name="{{ $key }}" id="controller" class="formcontrol2" onchange="applyFilters('controller')">
            <option value="">All {{ $filter['label'] }}</option>
            @foreach ($filter['values'] as $id => $value)
            <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @endif

    </div>

</div>
@endforeach
@endif

@if (isset($filter['filtertype']) && $filter['filtertype'] === 'advanced')
<div class="col-md-3 " style="padding-top:0px">

    <button class="btn btn-warning btn-lg text-white" id="filter" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    <i class="fa fa-filter"></i></i> Advanced Filter <i class="fa-solid fa-plus" id="expandIcon" style="display: none;"></i>
    </button>
</div>
@endif

<div class="collapse collapseExample" id="collapseExample" style="margin-bottom:20px;">
    <div class="filterbody">

        <div class="row">

        @if (isset($filterdata))
            @foreach($filterdata as $key => $filter)
                @if ($filter['filtertype'] === 'advanced')
           
            <div class="col-md-3" style="padding:0px 5px 0px 8px;">
                <div class="form-group" style="margin-bottom: 0.5rem;">


                    @if($filter['inputType'] == 'select')
                    <label class="label">{{ $filter['label'] }}</label>
                    <!------  SELECT------------>
                    <select class="formcontrol2 advanced" name="{{ $key }}" id="{{ $key }}">

                        <option value="">All {{ $filter['label'] }}</option>
                        @foreach ($filter['values'] as $id => $value)
                        <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    <!---- GROUP SELECT ------------->
                    @elseif($filter['inputType'] === 'selectgroup')
                    <label class="label">{{ $filter['label'] }}</label>
                    <select class="formcontrol2" id="{{ $key }}" name="{{ $key }}">
                        <option value="">All {{ $filter['label'] }}</option>
                        @foreach ($filter['values'] as $groupKey => $groupValues)
                        <optgroup label="{{ $groupKey }}">
                            @foreach ($groupValues as $id => $value)
                            <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>

                    <!----  SELECT ARRAY ------------->
                    @elseif($filter['inputType'] === 'selectarray')
                    <label class="label">{{ $filter['label'] }}</label>
                    <select class="formcontrol2" id="{{ $key }}" name="{{ $key }}">
                        <option value="">All {{ $filter['label'] }}</option>
                        @foreach ($filter['values'] as $groupKey => $groupValues)
                        @php
                        $ids = $groupValues->pluck('id')->implode(',');
                        @endphp
                        <option value="{{ $ids }}" {{ request($key) == $ids ? 'selected' : '' }}>{{ $groupKey }}</option>
                        @endforeach
                    </select>


                    <!--------  DATE -------------->
                    @elseif($filter['inputType'] === 'date')
                    <label class="label">{{ $filter['label'] }}</label>
                    <input type="date" class="form-control advanced" id="{{ $key }}" name="{{ $key }}" value="{{ request($key) ??  now()->toDateString() }} }}" />
                    @endif

                </div>
            </div>
             @endif
            @endforeach
            <div class="col-md-3 ms-auto text-end" style="padding:35px 10px 0px 10px">
                <button type="submit" class="btn btn-primary btn-lg text-white mt-0 me-0 nextbutton">Apply Filter</button>
            </div>


            @else
            <div class="col-md-12">
                <h6>Filter not available.</h6>
            </div>
            @endif
        </div>


    </div>
</div>
<script>
    $(document).ready(function() {
        // Listen for changes in the select dropdown
        $('.mainfilter').on('change', function() {
            var query = this.value;
            //  alert(query);
            // Trigger form submission
            $('.filterForm').submit();
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Check if there are any filters applied
        $(".advanced").each(function() {
            var $input = $(this);

            if ($input.val() !== "") {
                // If yes, show the collapsible div
                $(".collapseExample").collapse("show");

                // For debugging, you can alert the value of the input
                //  alert($input.val());
            }
        });
    });
</script>

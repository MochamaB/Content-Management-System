
<form id="dateRangeForm" action="{{ url($routeParts[0]) }}" method="GET">
    <div class="d-flex align-items-center">
        <!-- Select Input -->
        <div class="form-group mr-2 mb-0" style="color: #000;font-weight: 600;font-size: 0.8rem;">
            @foreach($filterdata as $key => $filter)
            <select name="{{ $key }}" id="property" class="formcontrol2 calendar" style="min-width: 200px;">
                <option value="">All {{ $filter['label'] }}</option>
                @foreach ($filter['values'] as $id => $value)
                <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            @endforeach
        </div>
        <!-- Date Range Input -->
        <div class="input-group">
            <span class="input-group-text" style="border-right: none; background-color: #ffffff; border: 1px solid #ced4da;">
                <i class="mdi mdi-calendar" style="font-size: 16px;"></i>
            </span>
            <input type="text" class="form-control calendar" id="daterange" name="" style="max-width: 200px; border-left: none; height: calc(2.25rem + 2px);" />
        </div>

        <input type="hidden" name="from_date" id="from_date" value="">
        <input type="hidden" name="to_date" id="to_date" value="">
    </div>
</form>
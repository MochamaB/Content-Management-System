<style>

    .input-group {
    display: inline-flex;
    align-items: center;
    background-color: #E9E9E9;
    border: 1px solid #ced4da;
    border-radius: 1.25rem;
    padding: 0 0.75rem;
    font-size: 14px;
    margin-bottom: 0.5rem;
}
.input-group .calendar {
    background-color: #E9E9E9 !important;
    border: 2px solid #E9E9E9 !important;
    font-size: 0.9rem;
    font-weight: 700;
    line-height: 20px;
    text-transform: capitalize;
    height: 30px !important;
    padding: 0 8px;
    border-radius: 0px;
    display: inline-block;
    box-sizing: border-box;
    width: auto; /* Dynamic width */
    min-width: 100px; /* Minimum width */
    max-width: 120px; /* Maximum width */
    overflow: hidden; /* Ensure text doesn't overflow */
    text-overflow: ellipsis; /* Add ellipsis for long text */
    white-space: nowrap; /* Prevent wrapping */
    border-left: none !important;
}
</style>
<form id="dateRangeForm" action="{{ url($routeParts[0]) }}" method="GET">
<div class="d-sm-flex justify-content-between align-items-start">
    <div>
        
    </div>
    @if(isset($filters))

    <div>
        <div class="input-group">
            <i class="mdi mdi-calendar" style="font-size: 16px; vertical-align: middle; padding-right: 5px;"></i>
            <input type="text" class="form-control calendar" id="daterange" name="" placeholder="Date" />
            <span class="input-group-text" style="background-color: transparent; border: none; padding: 0;">
                <i class="mdi mdi-chevron-down" style="font-size: 18px; vertical-align: middle;"></i>
            </span>
            
        </div>

        <input type="hidden" name="from_date" id="from_date" value="">
        <input type="hidden" name="to_date" id="to_date" value="">



    </div>
    @endif
</div>
</form>
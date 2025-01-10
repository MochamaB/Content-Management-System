<style>
    .date-group {
        display: inline-flex;
        align-items: center;
        background-color: #E9E9E9;
        border: 1px solid #E9E9E9;
        border-radius: 1.25rem;
        padding: 0 0.75rem;
        font-size: 14px;
        margin-bottom: 0.5rem;
        border-left: none !important;
    }
    

    .input-icon {
        font-size: 16px;
        padding-right: 5px;
        display: flex;
        align-items: center;
    }

    .date-group .calendar {
        background-color: #E9E9E9 !important;
        border: 2px solid #E9E9E9 !important;
        font-size: 0.9rem;
        font-weight: 700;
        color: #212529;
        line-height: 20px;
        text-transform: capitalize;
        height: 30px !important;
        padding: 0 8px;
        border-radius: 0px;
        display: inline-block;
        box-sizing: border-box;
        width: auto;
        /* Dynamic width */
        min-width: 100px;
        /* Minimum width */
        max-width: 100px;
        /* Maximum width */
        text-overflow: ellipsis;
        /* Add ellipsis for long text */
        white-space: nowrap;
        /* Prevent wrapping */
        border-left: none !important;
    }
    .input-group-text {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1;
    color: #212529;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border: 1px solid #E9E9E9;
    border-radius: 2px;
}
</style>

<div class="d-sm-flex justify-content-between align-items-start">
    <div>

    </div>


    <div>

        <div class="date-group">
            <i class="mdi mdi-calendar input-icon"></i>
            <input
                type="text"
                class="calendar"
                id="daterange"
                name=""
                placeholder="Date" />
            <span class="input-group-text">
                <i class="mdi mdi-chevron-down chevron-icon"></i>
            </span>
        </div>
        <input type="hidden" name="from_date" id="from_date" value="{{ request('from_date', '') }}">
        <input type="hidden" name="to_date" id="to_date"  value="{{ request('to_date', '') }}">






    </div>

</div>
<style>
.row {
    display: flex;
    margin-bottom: 0.5rem;
}

.equal-height {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: white;
    padding: 1rem;
    border: 1px solid #ddd;
}

/* Optional: Add if you need scrolling for very tall content */
.equal-height-content {
    overflow-y: auto;
}
</style>

@if(isset($dashboardConfig))
    @foreach($dashboardConfig['rows'] as $row)
    <div class="row gx-3">
        @foreach($row['columns'] as $column)
        <div class="{{ $column['width'] }}">
            <div class="equal-height">
                <div class="equal-height-content">
                    @include($column['component'], $column['data'])
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
@endif
<style>

.equal-height {
    max-height: 250px;
    min-height: 250px;
    overflow: auto;
    display: flex;
    flex-direction: column;
}
</style>
@if(isset($dashboardConfig))
    @foreach($dashboardConfig['rows'] as $row)
        <div class="row mb-2 gx-3" style="display: flex; align-items: stretch">
            @foreach($row['columns'] as $column)
                <div class="{{ $column['width'] }}">
                    <div class="p-4 border bg-white equal-height">
                        @include($column['component'], $column['data'])
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endif



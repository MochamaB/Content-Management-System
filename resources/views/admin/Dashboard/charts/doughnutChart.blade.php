<style>
    /* Chart Container Styling */
    .chart-container {
        width: 200px;
    }

    /* Legend Container */
    #doughnutChart-legend {
        flex: 1;
        min-width: 150px;
        margin: 0;
        padding: 0;
        text-align: left;
        /* Prevents it from becoming too narrow */
    }

    /* Legend List Styling */
    #doughnutChart-legend ul {
        padding: 0;
        list-style-type: none;
        text-align: left;
    }

    #doughnutChart-legend li {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    /* Color Box for Legend */
    #doughnutChart-legend li span {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 10px;
        border-radius: 50%;
        /* Makes the color box circular */
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .chart-legend-container {
            flex-direction: column;
            /* Stacks the chart and legend */
            align-items: center;
        }

        #doughnutChart-legend {
            margin-top: 20px;
            /* Add some spacing when stacked */
        }
    }
</style>
<div class="d-flex justify-content-between align-items-start">
    <div>
    <h5 class="card-title card-title-dash pt-1"><b>{{$title ?? ''}}</b></h5>
    </div>
    <div>

    </div>
</div>
<div class="d-lg-flex justify-content-between">
    <div class="chart-container mt-3">
        <canvas class="my-auto" id="doughnutChart" style="display: block; box-sizing: border-box; height: 210px; width: 210px;" width="210"></canvas>
    </div>
    <div id="doughnutChart-legend" class="mt-3 text-center"></div>
</div>
<script>
    const doughnutChartData = @json($chartData); // Pass chartData dynamically from PHP
    console.log(chartData);
</script>
<style>
    /* General Flexbox Container */
    .chart-legend-container {
        display: flex;
        flex-wrap: wrap;
        /* Allows wrapping for responsiveness */
        justify-content: end;
        align-items: center;
        gap: 0px;
        /* Adds spacing between chart and legend */
    }

    /* Chart Container Styling */
    .chart-container {
        max-width: 300px;
        /* Adjust as needed */
        max-height: 300px;
        flex: 1;
        /* Allows flexibility in size */
    }

    /* Legend Container */
    #doughnutChart-legend {
        flex: 1;
        /* Legend takes equal space as the chart */
        min-width: 150px;
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

<div class="chart-legend-container">
    <div class="chart-container">
        <canvas class="my-auto" id="doughnutChart"></canvas>
    </div>
    <div id="doughnutChart-legend" class="mt-2 text-center"></div>
</div>
<script>
    const doughnutChartData = @json($chartData); // Pass chartData dynamically from PHP
    console.log(chartData);
</script>
<div class="row flex-grow">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">Invoice Payments</h4>
                        <p class="card-subtitle card-subtitle-dash">Amount Invoiced against Payments Made</p>
                    </div>
                    
                </div>
                <div class="d-sm-flex align-items-center mt-1 justify-content-between">
                    <div class="d-sm-flex align-items-center mt-4 justify-content-between">
                        <h4 class="me-2">{{ $sitesettings->site_currency }}</h4>
                        <h2 class="me-2 fw-bold" id="firstTotal"></h2>
                        <h4 class="text-success" id="percentage"></h4>
                    </div>
                    <div class="me-3">
                        <div id="marketing-overview-legend"></div>
                    </div>
                </div>
                <div class="chartjs-bar-wrapper mt-3">
                    <canvas id="marketingOverview"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  var chartData = @json($data);

console.log(chartData); // Debugging purpose: Check the console to see the data structure

</script>
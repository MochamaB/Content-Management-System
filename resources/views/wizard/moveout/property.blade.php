@php
$securityDepositCharge = $lease->unitcharges()
->where('charge_name', 'security deposit')
->first();
$securityDeposit = $securityDepositCharge ? $securityDepositCharge->rate : 0;
$totalCosts = $leaseItems->sum('cost'); 
@endphp
<h5>Property Item Condition</h5>
<hr>

<div class="row mt-3 mb-3">

    <div class="col-md-4">
        <p class="text-muted">SECURITY DEPOSIT</p>
        <h3 style="color:#1F3BB3">{{ $sitesettings->site_currency }} {{$securityDeposit}}</h3>
    </div>
    <div class="col-md-1">
        <h3> - </h3>
    </div>
    <div class="col-md-3">
        <p  class="text-muted">TOTAL COSTS</p>
        <h3 class="total-costs" style="color:#ffaf00">
            {{ $sitesettings->site_currency }} @currency($totalCosts) </h3>
           
    </div>
    <div class="col-md-1">
    <h3> = </h3>
    </div>
    <div class="col-md-3 balance">
        <p  class="text-muted">BALANCE</p>
        <h3 class="balance">
        {{ $sitesettings->site_currency }} 0 </h3>
    </div>

</div>
<form method="POST" action="{{ url('lease/' . $lease->id . '/propertyCondition') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-sticky-header="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">ITEM</th>
                <th class="text-center">CONDITION</th>
                <th class="text-center">COST</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaseItems as $category => $items)
            <!-- Display Category Header Row -->
            <tr>
                <td colspan="5" class="bg-light text-left" style="font-weight:bold; font-size:14px;color:#1F3BB3">
                    {{ strtoupper($category) }}
                </td>
            </tr>
            <!-- Display Each Item -->
            @foreach($items as $index => $item)
            <tr style="height:35px;">
                <td class="">{{ $loop->iteration }}</td>
                <td class="text-left">
                    {{ $item->defaultLeaseItem->item_description ?? 'N/A' }}
                    <!-- Hidden Input for lease_item_id -->
                    <input type="hidden" name="default_item_id[]" value="{{ $item->default_item_id }}">
                </td>
                <td class="text-center" style="padding:0px">
                    <select name="condition[]" class="formcontrol2" required>
                        <option value="">Select Condition</option>
                        <option value="Good" {{ $item->condition == 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Needs Repair" {{ $item->condition == 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                        <option value="Damaged" {{ $item->condition == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="Needs Replacement" {{ $item->condition == 'Needs Replacement' ? 'selected' : '' }}>Needs Replacement</option>
                    </select>
                </td>

                <td class="text-center" style="padding:0px">
                    <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                            {{ $sitesettings->site_currency }}.
                        </span>
                        <input type="number" class="form-control amount" name="cost[]"
                            value="{{ $item->cost ?? 0 }}" style="text-align: left; padding-left: 45px;" required>
                    </div>
                </td>
            </tr>
            @endforeach
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No Lease Items Found</td>
            </tr>
            @endforelse

        </tbody>
    </table>


    @include('admin.CRUD.wizardbuttons')
</form>
<script>
   $(document).ready(function() {
    // Function to calculate total costs and balance
    function calculateTotals() {
        let totalCosts = 0;

        // Sum all values of the 'cost[]' inputs
        $('#table tbody .amount').each(function() {
            const cost = parseFloat($(this).val()) || 0; // Default to 0 if invalid
            totalCosts += cost;
        });

        // Display the total costs in the respective <h3>
        $('.total-costs').html(`{{ $sitesettings->site_currency }} ${totalCosts.toFixed(2)}`);

        // Calculate balance (Security Deposit - Total Costs)
        const securityDeposit = parseFloat('{{ $securityDeposit }}') || 0;
        const balance = securityDeposit - totalCosts;

        // Determine the color for the balance
        const balanceColor = balance >= 0 ? 'green' : 'red';

        // Update Balance with dynamic color
        $('.balance h3').html(`
            <span style="color: ${balanceColor};">{{ $sitesettings->site_currency }} ${balance.toFixed(2)}</span>
        `);
    }

    // Recalculate totals on input change
    $('#table').on('input', '.amount', function() {
        calculateTotals();
    });

    // Initial calculation on page load
    calculateTotals();
});

</script>
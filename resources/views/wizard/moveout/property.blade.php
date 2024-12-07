@php
$securityDepositCharge = $lease->unitcharges()
->where('charge_name', 'security deposit')
->first();
$securityDeposit = $securityDepositCharge ? $securityDepositCharge->rate : 0;
@endphp
<h4>Property Item Condition</h4>
<hr>
<div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6">
        <ul class="ml-2 px-3 list-unstyled" style="text-align: right;">
            <li>
                <h5><b>SECURITY DEPOSIT:</b> {{ $sitesettings->site_currency }} {{$securityDeposit}}</h5>
            </li>
            <li class="total-costs">
                <h5><b>TOTAL COSTS:</b> {{ $sitesettings->site_currency }} 0 </h5>
            </li>
            <li class="balance ">
                <h5><b>BALANCE:</b> {{ $sitesettings->site_currency }} 0 </h5>
            </li>
            <li></br></li>
            <!-- Render the badge -->

        </ul>
    </div>
</div>
<form method="POST" action="{{ url('lease/' . $lease->id . '/propertyCondition') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

<table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-sticky-header="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
    <thead>
        <tr>

            <th class="text-center"></th>
            <th class="text-center">ITEM DESCRIPTION</th>
            <th class="text-center">CONDITION</th>
            <th class="text-center">COST</th>
            <th class="text-center">ACTION</th>


        </tr>
    </thead>
    <tbody>

        <tr style="height:35px;">
            <td>1.</td>
            <td class="text-center" style="padding:0px">
                <input type="text" class="form-control" name="item[]" id="" value="" required>
            </td>
            <td class="text-center" style="padding:0px">
                <select name="condition" id="" class="formcontrol2 " placeholder="Select" required>
                    <option value="">Select Value</option>
                    <option value="">Good</option>
                    <option value="">Needs Repair</option>
                    <option value="">Damaged</option>
                    <option value="">Needs Replacement</option>
                </select>
            </td>

            <td id='' class="text-center" style="padding:0px">
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                    </span>
                    <input type="number" class="form-control amount money" name="cost[]" value="0" style="text-align: left; padding-left: 45px;" required>
                </div>
            </td>
            <td class="text-center" style="background-color:#dae3fa;padding-right:20px">
                <h5>
                    <a href="#" class="add-expense"><i class="menu-icon mdi mdi-plus-circle"> Add </i></a>
                </h5>
            </td>


        </tr>

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

            // Display the total costs in the respective <li>
            $('li.total-costs').html(`
             <h5><b>TOTAL COSTS:</b> {{ $sitesettings->site_currency }} ${totalCosts.toFixed(2)}</h5>`);

            // Calculate balance (Security Deposit - Total Costs)
            const securityDeposit = parseFloat('{{ $securityDeposit }}') || 0;
            const balance = securityDeposit - totalCosts;

            // Determine the color for the balance
            const balanceColor = balance >= 0 ? 'blue' : 'red';

            // Update Balance with the h5 tag and dynamic color
            $('li.balance').html(`
            <h5 style="color: ${balanceColor};"><b>BALANCE:</b> {{ $sitesettings->site_currency }} ${balance.toFixed(2)}</h5>
            `);
        }

        // Recalculate totals on input change
        $('#table').on('input', '.amount', function() {
            calculateTotals();
        });

        // Add a new row when "Add Expense" is clicked
        $('#table').on('click', '.add-expense', function(e) {
            e.preventDefault(); // Prevent default behavior

            // Clone the last row and clear the inputs
            const newRow = $('#table tbody tr:last').clone();
            newRow.find('input').val('');
            newRow.find('td:first').text($('#table tbody tr').length + 1);
            newRow.find('td:last').html('<a href="#" class="remove-expense"><i class="ti-close" style="font-size: 16px; color: red; font-weight: bold;"> Remove</i></a>');
            $('#table tbody').append(newRow);

            calculateTotals(); // Recalculate totals after adding a row
        });

        // Remove a row when "Remove" is clicked
        $('#table').on('click', '.remove-expense', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();

            // Update row numbers
            $('#table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });

            calculateTotals(); // Recalculate totals after removing a row
        });

        // Initial calculation on page load
        calculateTotals();
    });
</script>
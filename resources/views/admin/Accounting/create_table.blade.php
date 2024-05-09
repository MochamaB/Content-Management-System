<div class="col-md-12">
    <table id="table" data-toggle="table" 
        data-icon-size="sm" 
        data-buttons-class="primary" 
        data-toolbar-align="right" 
        data-buttons-align="left" 
        data-search-align="left" 
        data-sort-order="asc" 
        data-mobile-responsive="true"
        data-sticky-header="true" 
        data-page-list="[100, 200, 250, 500, ALL]"
        data-page-size="100" 
        data-show-footer="false" 
        data-side-pagination="client"
        class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">ACCOUNT</th>
                <th class="text-center">DESCRIPTION</th>
                <th class="text-center">AMOUNT</th>
                <th class="text-center">ACTION</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height:35px;">
                <td>1.</td>
                <td class="text-center" style="padding:0px">
                    <select name="chartofaccount_id[]" id="chartofaccount_id" class="formcontrol2" placeholder="Select" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $accounttype => $account)
                        <optgroup label="{{ $accounttype }}">
                            @foreach($account as $item)
                            <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </td>
                <td class="text-center" style="padding:0px">
                    <input type="text" class="form-control " name="description[]" value="" required>
                </td>
                <td id='' class="text-center" style="padding:0px">
                    <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                        </span>
                        <input type="number" class="form-control money" name="amount[]" value="" placeholder="0" style="text-align: left; padding-left: 45px;" required>
                    </div>
                </td>
                <td class="text-center" style="background-color:#dae3fa;padding-right:20px">
                    <h5><a class=" split_rent" id="addexpense"><i class="menu-icon mdi mdi-plus-circle"> Add Item </a></i>
                    </h5>
                </td>


            </tr>

        </tbody>
    </table>

  
</div>
<script>
    $(document).ready(function() {

        // When the "Add Another item" link is clicked
        $('#addexpense').on('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Clone the last row (including input fields)
            var newRow = $('#table tbody tr:last').clone();

            // Clear input values in the cloned row
            newRow.find('input').val('');
            // Set the initial value of the total cell to 0
            //    newRow.find('.total').text(`{{$sitesettings->site_currency}}. 0`);

            // Increment the numbering of the first td in the new row
            var currentCount = $('#table tbody tr').length;
            newRow.find('td:first').text(currentCount + 1);

            // Append the remove icon to the cloned row
            newRow.find('td:last').html('<i class="remove ti-close" style="font-size: 16px; color: red; font-weight: bold;"> Remove</i> ');

            // Append the cloned row to the table
            $('#table tbody').append(newRow);
        });

        // When the "Remove" button is clicked
        $('#table').on('click', '.remove', function() {
            // Remove the row
            $(this).closest('tr').remove();

            // Update the numbering of the remaining rows
            $('#table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        });
    });
</script>
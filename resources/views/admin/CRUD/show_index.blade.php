


    @if( Auth::user()->can($controller.'.create') || Auth::user()->id === 1)
        @if($controller !== "unitcharge")
            <a href="{{ url($controller.'/create',['id' => $id ?? '']) }}" class="btn btn-primary btn-lg text-white mb-0 me-0 float-end" role="button" style="text-transform: capitalize;">
                <i class="mdi mdi-plus-circle-outline"></i>
                Add New {{$controller}}
            </a>
        @endif
    @endif
<br /><br /><br />
<div class=" contwrapper">
    <div class="row">    
        <hr>
        @include('admin.CRUD.table', ['data' => $tableData])

    </diV>
</div>
<script>
    function applyFilters(filterField) {
        var selectedValues = {}; // Object to store the selected values

        // Iterate over each filter input
        $('.formcontrol2').each(function() {
            var filterField = $(this).attr('id').replace('Filter', ''); // Extract the filter field name
            var selectedValue = $(this).val(); // Get the selected value

            selectedValues[filterField] = selectedValue; // Store the selected value in the object
        });

        // Show all table rows
        $('#dataTable tbody tr').show();

        // Iterate over each selected filter
        $.each(selectedValues, function(filterField, selectedValue) {
            if (selectedValue !== 'All') {
                // Special handling for date filter field

                // Iterate over each table row
                $('#dataTable tbody tr').each(function() {
                    var row = $(this);
                    var columnValue = row.find('td:contains(' + selectedValue + ')');

                    // Hide the row if it does not match the selected filter value
                    if (columnValue.length === 0) {
                        row.hide();
                    }
                });
            }
        });
        // Display the selected value in an alert


    }
</script>
@include('layouts.admin.adminfooter')
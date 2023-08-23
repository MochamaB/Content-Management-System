
$(document).ready(function (e) {


    $('#logo').change(function () {

        let reader = new FileReader();

        reader.onload = (e) => {

            $('#logo-image-before-upload').attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);

    });

    $('#flavicon').change(function () {

        let reader = new FileReader();

        reader.onload = (e) => {

            $('#flavicon-image-before-upload').attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);

    });

    /////////// Master filter show and hide
    $('#collapseExample').on('show.bs.collapse', function () {
        $('#collapseIcon').hide();
        $('#expandIcon').show();
    });

    $('#collapseExample').on('hide.bs.collapse', function () {
        $('#expandIcon').hide();
        $('#collapseIcon').show();
    });

    function closeCollapse() {
        // Find the collapse element by its ID
        const collapseElement = document.getElementById("collapseExample");

        // Use Bootstrap's collapse method to close it
        if (collapseElement) {
            const collapse = new bootstrap.Collapse(collapseElement);
            collapse.hide();
        }
    }

    ////////MASTER FILTER JS

    function applyFilters(filterField) {
        var selectedValues = {}; // Object to store the selected values

        // Iterate over each filter input
        $('.formcontrol2').each(function () {
            var filterField = $(this).attr('id').replace('Filter', ''); // Extract the filter field name
            var selectedValue = $(this).val(); // Get the selected value

            selectedValues[filterField] = selectedValue; // Store the selected value in the object
        });

        // Show all table rows
        $('#dataTable tbody tr').show();

        // Iterate over each selected filter
        $.each(selectedValues, function (filterField, selectedValue) {
            if (selectedValue !== 'All') {
                // Special handling for date filter field

                // Iterate over each table row
                $('#dataTable tbody tr').each(function () {
                    var row = $(this);
                    var columnValue = row.find('td:contains(' + selectedValue + ')');

                    // Hide the row if it does not match the selected filter value
                    if (columnValue.length === 0) {
                        row.hide();
                    }
                });
            }
        });
    }
});

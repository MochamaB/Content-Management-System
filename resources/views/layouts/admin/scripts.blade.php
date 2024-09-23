<!---- Fetch Units ----->
<script>
    $(document).ready(function() {
        $('#property_id').on('change', function() {
            var query = this.value;
            // Clear existing unit options before appending new ones
            $('#unit_id').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-units')}}",
                type: "POST",
                data: {
                    property_id: query,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {

                    // Loop through the properties of the data object
                    for (var unitId in data) {
                        if (data.hasOwnProperty(unitId)) {
                            // Access each unit ID and unit number
                            var unitNumber = data[unitId];
                            console.log('Unit ID: ' + unitId + ', Unit Number: ' + unitNumber);

                            // You can use these values as needed, for example, to populate a select element
                            // Here's an example of adding options to a select element with the id "unit_id"
                            $('#unit_id').append(new Option(unitNumber, unitId));
                        }
                    }

                }
            });

        });


    });
</script>

<!---- Fetch Last reading----->
<script>
    $(document).ready(function() {
        $('#unitcharge_id').on('change', function() {
            var inputValue = $(this).val();
            var propertyId = $('#property_id').val();
            var unitId = $('#unit_id').val(); // Get unit_id from input

            // alert(unitId);

            // Clear existing unit options before appending new ones
            $('#lastreading').empty();
            $('#startdate').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-meterReading')}}",
                type: "POST",
                data: {
                    unitcharge_id: inputValue,
                    property_id: propertyId, // Pass property_id to the server
                    unit_id: unitId,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var currentReadingValue = data.currentreading;
                    var endDateValue = data.enddate;
                    if (data && data.currentreading && data.enddate) {
                        //  alert(endDateValue);
                        $('#lastreading').val(currentReadingValue);
                        $('#startdate').val(endDateValue);
                    } else {
                        $('#lastreading').val("0.00");
                        $('#lastreading').prop('readonly', false);
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = String(today.getMonth() + 1).padStart(2, '0'); // Add leading zero if needed
                        var day = String(today.getDate()).padStart(2, '0'); // Add leading zero if needed
                        var formattedDate = year + '-' + month + '-' + day;
                        $('#startdate').val(formattedDate);
                    }

                }
            });

        });


    });
</script>


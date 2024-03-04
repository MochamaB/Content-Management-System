@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">
    <h4 style="text-transform: capitalize;"><b> </b></h4>
    <hr>

    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Select Property<span class="requiredlabel">*</span></label>
            <select name="property_id" id="" class="formcontrol2 property_id" placeholder="Select" required>
                <option value=""> 'Select Property'</option>
                @foreach($properties as $item)
                <option value="{{ $item->id }}">{{ $item->property_name }}</option>
                @endforeach
            </select>

        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label"> Select Unit<span class="requiredlabel">*</span></label>
            <select name="unit_id" id="" class="formcontrol2 unit_id" placeholder="Select" required>

                <option value=""></option>

            </select>
        </div>
    </div>
    <div class="col-md-4">
        <button type="" id="select" class=" btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="select">Create {{$routeParts[0]}}</button>
    </div>




</div>
<!---- Fetch Units ----->
<script>
    $(document).ready(function() {
        $('.property_id').on('change', function() {
            var query = this.value;
            // Clear existing unit options before appending new ones
            $('.unit_id').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{url('api/fetch-allunits')}}",
                type: "POST",
                data: {
                    property_id: query,

                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(data) {
                    // Add an initial message option
                    $('.unit_id').append(new Option('Select Value Below', ''));
                    // Loop through the properties of the data object
                    for (var unitId in data) {
                        if (data.hasOwnProperty(unitId)) {
                            // Access each unit ID and unit number
                            var unitNumber = data[unitId];
                            console.log('Unit ID: ' + unitId + ', Unit Number: ' + unitNumber);

                            // You can use these values as needed, for example, to populate a select element
                            // Here's an example of adding options to a select element with the id "unit_id"
                            $('.unit_id').append(new Option(unitNumber, unitId));
                        }
                    }

                }
            });

        });
        $('#select').on('click', function() {
            // Get the value of the property_id field
            var selectedpropertyId = $('.property_id').val();

            // Append unit_id to the current URL
            var newUrl = window.location.href + '/' + selectedpropertyId + '/properties';

            // Load the new URL
            window.location.href = newUrl;
        });
        $('.unit_id').on('change', function() {
            var selectedUnitId = $(this).val();

            // Append unit_id to the current URL
            var newUrl = window.location.href + '/' + selectedUnitId + '/units';

            // Load the new URL
            window.location.href = newUrl;
        });



    });
</script>




@endsection
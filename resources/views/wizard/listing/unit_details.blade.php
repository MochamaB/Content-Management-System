@if(($routeParts[1] === 'create'))
<h5><b> Unit Details</b></h5>
<hr>
<form method="POST" action="{{ url('unitdetails') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Select Property<span class="requiredlabel">*</span></label>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required>
                <option value="{{$selectedProperty->id ?? ''}}">{{$selectedProperty->property_name ?? 'Select Property'}}</option>
                @foreach($properties as $item)
                <option value="{{ $item->id }}">{{ $item->property_name }}</option>
                @endforeach
            </select>

        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label"> Select Unit<span class="requiredlabel">*</span></label>
            <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required>

                <option value="{{$unit->id ?? ''}}">{{$unit->unit_number ?? 'Select Property First'}}</option>

            </select>
        </div>
    </div>
    @include('admin.CRUD.wizardbuttons')
</form>

@elseif(($routeParts[1] === 'edit'))
@endif
<script>
    $(document).ready(function() {
        $('#property_id').on('change', function() {
            var query = this.value;

            // Clear the unit dropdown and remove 'is-invalid' class
            $('#unit_id').empty().append(new Option('Loading...', '')).removeClass('is-invalid');

            // AJAX setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Perform the AJAX request
            $.ajax({
                url: "{{ url('api/fetch-Listingunits') }}", // Adjusted URL
                type: "POST",
                data: {
                    property_id: query,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    $('#unit_id').empty(); // Fully clear previous options before populating new ones

                    // Check if data is empty
                    if ($.isEmptyObject(data)) {
                        $('#unit_id').append(new Option('No matching records found', ''));
                        $('#unit_id').addClass('is-invalid');
                    } else {
                        // Add the default option
                        $('#unit_id').append(new Option('Select Value Below', ''));

                        // Populate the dropdown with new options
                        $.each(data, function(unitId, unitNumber) {
                            $('#unit_id').append(new Option(unitNumber, unitId));
                        });
                    }
                },
                error: function() {
                    $('#unit_id').empty().append(new Option('Error loading units', ''));
                    $('#unit_id').addClass('is-invalid');
                }
            });
        });
    });
</script>

    <div class="filterbody">
        <form method="GET" action="{{ url()->current() }}">
            <div class="row">

                @if (isset($filterdata))
                @foreach($filterdata as $key => $filter)
                <div class="col-md-3" style="padding:0px 5px 0px 8px;" >
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="label">{{ $filter['label'] }}</label>

                        @if($filter['inputType'] == 'select')
                        <!------  SELECT------------>
                        <select class="formcontrol2" name="{{ $key }}" id="{{ $key }}">

                            <option value="">All {{ $filter['label'] }}</option>
                            @foreach ($filter['values'] as $id => $value)
                            <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <!---- GROUP SELECT ------------->
                        @elseif($filter['inputType'] === 'selectgroup')
                        <select class="formcontrol2" id="{{ $key }}" name="{{ $key }}">
                            <option value="">All {{ $filter['label'] }}</option>
                            @foreach ($filter['values'] as $groupKey => $groupValues)
                            <optgroup label="{{ $groupKey }}">
                                @foreach ($groupValues as $id => $value)
                                <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>

                        <!--------  DATE -------------->
                        @elseif($filter['inputType'] === 'date')
                        <input type="date" class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ request($key) }}" />
                        @endif

                    </div>
                </div>
                @endforeach
                <div class="col-md-3 ms-auto text-end" style="padding:35px 10px 0px 10px">
                    <button type="submit" class="btn btn-primary btn-lg text-white mt-0 me-0 nextbutton">Apply Filter</button>
                </div>


                @else
                <div class="col-md-12">
                    <h4>Filter not available.</h4>
                </div>
                @endif
            </div>
        </form>

    </div>

<!---- Fetch Units ----->
<script>
    $(document).ready(function() {
        $('.property_id').on('change', function() {
            var query = this.value;
            alert(query);
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

    });
</script>
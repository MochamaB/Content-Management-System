@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

   
    <form method="POST" action="{{ url('ticket') }}" class="myForm" novalidate>
        @csrf
        @if ($property)
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2 property_id" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        @else
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Select Property<span class="requiredlabel">*</span></label>
                <select name="property_id" id="property_id" class="formcontrol2 property_id" placeholder="Select" required>
                    <option value=""> 'Select Property'</option>
                    @foreach($properties as $item)
                    <option value="{{ $item->id }}">{{ $item->property_name }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label class="label"> Select Unit <span class="">(OPTIONAL)</span></label>
                <select name="unit_id" id="" class="formcontrol2 unit_id" placeholder="Select">

                    <option value="">Select Property first</option>

                </select>
            </div>
        </div>
        @foreach($fields as $field => $attributes)
        <div class="col-md-6">
            <div class="form-group">
                <!--- LABEL -->
                <label class="label" id="label-{{ $field }}">{{ $attributes['label'] }}
                    @if ($attributes['required'])
                    <span class="requiredlabel">*</span>
                    @endif
                </label>
                <!---- NORMAL SELECT ------------->
                @if($attributes['inputType'] === 'select')
                <select class="formcontrol2 @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}">
                <option value=""> Select Value'

                
                </option>
                    @foreach ($data[$field] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                    
                </select>
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" id="exampleTextarea1" name="{{ $field }}" rows="4" columns= "6" @if($attributes['required']) required @endif>
                
                </textarea>
                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid  @enderror" id="{{ $field }}" name="{{ $field }}"  value="{{ old($field) }}" 
                @if($attributes['required']) required @endif  @if($attributes['readonly']) readonly @endif>
                @endif



            </div>
        </div>
        @endforeach
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="submit">Create {{$routeParts[0]}}</button>
        </div>


        </form>
</div>

<!---- Fetch Units ----->
<script>
    $(document).ready(function() {
         // Call the function on page load
    fetchAllUnits();

        $('.property_id').on('change', function() {
        // Call the function on change
        fetchAllUnits();
    });
    function fetchAllUnits() {
        var query = $('.property_id').val();
       // alert(query);
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
    }

    });
    
</script>

@endsection
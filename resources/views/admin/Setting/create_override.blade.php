@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h5>New Over Ride Setting</h5>
    <hr>
    <form method="POST" action="{{ url('setting') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Model<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="model_type" value="{{$modelClass}}" required readonly>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Setting Key<span class="requiredlabel">*</span></label>
                <select class="formcontrol2" id="key" name="key" required>
                    <option value="">Select Setting</option>
                    @foreach ($setting as $item)
                    <option value="{{ $item->key }}">{{ $item->key }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Value<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="value" name="value" value="" required>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{{class_basename($modelClass)}} Item to be overridden<span class="requiredlabel">*</span></label>
                <select class="formcontrol2" id="" name="model_id" required>
                    <option value="">Select Item</option>
                    @foreach ($options as $lease_id => $value)
                    <option value="{{ $lease_id }}">{{ $value }}</option>
                    @endforeach
                </select>

            </div>
        </div>
      




        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Setting</button>
        </div>

    </form>
</div>

<!---- Fetch Setting ----->
<script>
$(document).ready(function() {
    $('#key').on('change', function() {
        var selectedKey = this.value;

        // Clear existing value in the input before fetching new one and remove readonly
        $('#value').val('').removeAttr('readonly');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ url('api/fetch-setting') }}",
            type: "POST",
            data: {
                key: selectedKey,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var settingValue = data.value;

                    // If the value is "YES", change it to "NO"
                    if (settingValue === 'YES') {
                        $('#value').val('NO').attr('readonly', true);
                    } 
                    // Otherwise, display the actual value
                    else {
                        $('#value').val(settingValue).removeAttr('readonly');
                    }
                } else {
                    // If no data is returned, clear the value input
                    $('#value').val('').removeAttr('readonly');
                }
            },
            error: function(error) {
                console.log('Error fetching setting:', error);
                // Clear the input if there was an error
                $('#value').val('');
            }
        });
    });
});

</script>

@endsection
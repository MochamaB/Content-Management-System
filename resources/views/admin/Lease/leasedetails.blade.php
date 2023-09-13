<h4><b> New {{ $routeParts[0] }}</b></h4>
<hr>

<form method="POST" action="{{ url($routeParts[0]) }}" id="myForm" enctype="multipart/form-data" novalidate>
    @csrf
 
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Select Property<span class="requiredlabel">*</span></label>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required>
                <option value="{{$lease->property_id ?? ''}}">{{$lease->property->property_name ?? 'Select Property'}}</option>
                @foreach($properties as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>

        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label"> Select Unit<span class="requiredlabel">*</span></label>
            <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required>
                @if(!empty($lease))
                <option value="{{$lease->unit_id ?? ''}}">{{$lease->unit->unit_number ?? 'Select Unit'}}</option>
                @endif
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Add Tenant<span class="requiredlabel">*</span></label>
            <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select" required>
            <option value="{{$lease->user_id ?? ''}}">{{$lease->user->firstname  ?? 'Select'}} {{$lease->user->lastname  ?? 'Tenant'}}</option>
                @foreach($tenants as $key => $item)
                <option value="{{ $item->id }}">{{ $item->firstname }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Lease Period<span class="requiredlabel">*</span></label>
            <select name="lease_period" id="lease_period" class="formcontrol2" placeholder="Select" required>
            <option value="{{$lease->lease_period ?? ''}}">{{$lease->lease_period ?? 'Select Period'}}</option>
                <option value="open"> Open (Terminated at-will)</option>
                <option value="fixed">Fixed</option>

            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
    
            <input type="hidden" class="form-control" id="status" name="status" value="Active" readonly>
        </div>
    </div>
    <div class=row>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="{{$lease->startdate ?? ''}}"  required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="enddate">
                <label class="label">End Date<span class="requiredlabel"></span></label>
                <input type="date" class="form-control" name="enddate" value="{{$lease->enddate ?? ''}}" >
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 " id="">Next:Tenant Details</button>
            </div>
        </div>
    </div>
</form>

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
<script>
    $(document).ready(function() {
        const $enddate = $("#enddate");
        $enddate.hide();
        $('#lease_period').on('change', function() {
            var query = this.value;
            if (query === "fixed") {
                $enddate.show();
            }else{
                $enddate.hide();
            }

        });
    });
</script>
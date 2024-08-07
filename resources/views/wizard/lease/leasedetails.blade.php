@if(($routeParts[1] === 'create'))
<h4><b> New {{ $routeParts[0] }}</b></h4>
<hr>

<form method="POST" action="{{ url('leasedetails') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="col-md-8">
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
    <div class="col-md-8">
        <div class="form-group">
            <label class="label"> Select Unit<span class="requiredlabel">*</span></label>
            <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required>
              
                <option value="{{$lease->unit_id ?? ''}}">{{$lease->unit->unit_number ?? 'Select Unit'}}</option>
             
            </select>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Add Tenant <span class="requiredlabel">*</span></label>
            <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select" required>
                <option value="{{$lease->user_id ?? ''}}">{{$lease->user->firstname ?? 'Select'}} {{$lease->user->lastname ?? 'Tenant'}}</option>
                @foreach($tenants as $key => $item)
                <option value="{{ $item->id }}">{{ $item->firstname }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Lease Period<span class="requiredlabel">*</span></label>
            <select name="lease_period" id="lease_period" class="formcontrol2" placeholder="Select" required>
                <option value="{{$lease->lease_period ?? ''}}">{{$lease->lease_period ?? 'Select Period'}}</option>
                <option value="open"> Open (Terminated at-will)</option>
                <option value="fixed">Fixed</option>

            </select>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">

            <input type="hidden" class="form-control" id="status" name="status" value="Active" readonly>
        </div>
    </div>
    <div class=row>
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="{{$lease->startdate ?? ''}}" data-date-format="YYYY/MM/DD" required>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group" id="enddate">
                <label class="label">End Date<span class="requiredlabel"></span></label>
                <input type="date" class="form-control" name="enddate" value="{{$lease->enddate ?? ''}}">
            </div>
        </div>
    </div>

    @include('admin.CRUD.wizardbuttons')
</form>
@elseif(($routeParts[1] === 'edit'))

<div class=" contwrapper">
    <h4 style="text-transform: capitalize;">{{$routeParts[0]}} Information &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink">Edit</a>
    </h4>
    @endif
    <hr>

    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Lease Period<span class="requiredlabel">*</span></label>
            <h5>
                <small class="text-muted">
                    {{ $lease->lease_period}}
                </small>
            </h5>
            <select name="lease_period" id="lease_period" class="formcontrol2" placeholder="Select" required>
                <option value="{{$lease->lease_period ?? ''}}">{{$lease->lease_period ?? 'Select Period'}}</option>
                <option value="open"> Open (Terminated at-will)</option>
                <option value="fixed">Fixed</option>

            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label">Lease Status<span class="requiredlabel">*</span></label>
            <h5>
                <small class="text-muted">
                    {{ $lease->status}}
                </small>
            </h5>
            <select name="status" id="status" class="formcontrol2" placeholder="Select" required>
                <option value="{{$lease->status?? ''}}">{{$lease->status ?? 'Select Status'}}</option>
                <option value="Active"> Active</option>
                <option value="Suspended">Suspended</option>
                <option value="Expired">Expired</option>
                <option value="Terminated"> Terminated</option>
                <option value="Draft">Draft</option>

            </select>
        </div>
    </div>
    <div class=row>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Start Date<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $lease->startdate}}
                    </small>
                </h5>
                <input type="text" class="form-control" name="" value="{{ $lease->startdate ?? '' }}" readonly>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group" id="enddate">
                <label class="label">End Date<span class="requiredlabel"></span></label>
                <h5>
                    <small class="text-muted">
                        {{ $lease->enddate ?? 'Not set' }}
                    </small>
                </h5>
                <input type="date" class="form-control" name="enddate" value="{{ $lease->enddate ?? '' }}">
            </div>
        </div>
        <br /><br />
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Lease Details</button>
        </div>


    </div>
</div>


@endif

@include('layouts.admin.scripts')

<script>
    $(document).ready(function() {
        const $enddate = $("#enddate");
        const endDateValue = $enddate.val();
           //   alert(endDateValue);
        if (endDateValue === '') {
            $enddate.hide();
        }
        //    $enddate.show();
        $('#lease_period').on('change', function() {
            var query = this.value;
            if (query === "open") {
                $enddate.hide();
            } else {
                $enddate.show();
            }

        });
    });
</script>
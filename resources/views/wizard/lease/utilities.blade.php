@if(($routeParts[1] === 'create'))
<h4><b> Add Utilities </b></h4>
<hr>
<div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-information text-muted me-1"></i>
        <h5> Utilities are billed separately with rent in this property.</a></h5>
       
    </div>
</div><br />
<form method="POST" action="{{ url('assignutilities') }}" class="myForm" enctype="multipart/form-data" novalidate>
@csrf
    @if(!empty($utilities))
<div class="addfields" style="margin-bottom:30px">

@foreach ((empty($sessioncharges) ? collect($utilities) : collect($sessioncharges)) as $utility)
    <div class="row dynamicadd">
        <input type="hidden" class="form-control" id="property_id" name="property_id" value="{{$lease->property_id ?? ''}}">
        <input type="hidden" class="form-control" id="unit_id" name="unit_id" value="{{$lease->unit_id ?? ''}}">
        <input type="hidden" class="form-control" id="chartofaccounts_id" name="chartofaccounts_id[]" value="{{$utility['chartofaccounts_id'] ?? $utility->chartofaccounts_id ?? ''}}">
        <input type="hidden" class="form-control" id="utility_id" name="utility_id[]" value="{{$utility['utility_id'] ?? $utility->id ?? ''}}">
        <input type="hidden" class="form-control" name="rate[]" value="{{ $utility['default_rate'] ?? $utility->default_rate ?? ''}}" required>
        <input type="hidden" class="form-control" name="charge_type[]" value="{{$utility['charge_type'] ?? $utility->utility_type ?? ''}}">
            <h4>{{ $utility['charge_name'] ?? $utility->utility_name}} <span class="text-muted"></span></h4>
            <input type="hidden" class="form-control" id="charge_name" name="charge_name[]" value="{{ $utility['charge_name'] ?? $utility->utility_name}}">
        <div class="col-md-6">
        <div class="form-group">
            <label class="label">Bill Cycle<span class="requiredlabel">*</span></label>
            <select name="charge_cycle[]" id="charge_cycle" class="formcontrol2" placeholder="Select" required>
          
                <option value="{{$utility['default_charge_cycle']?? ''}}">{{$utility['default_charge_cycle'] ?? 'Select Bill Cycle'}}</option>
          
                <option value="Monthly"> Monthly</option>
                <option value="Twomonths">Two Months</option>
                <option value="Quaterly">Quaterly</option>
                <option value="Halfyear">6 Months</option>
                <option value="Year">1 Year</option>

            </select>
        </div>
    </div>
  
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate[]" value="{{$utility->startdate ?? $lease->startdate}}" required>
                <input type="hidden" class="form-control" id="recurring_charge" name="recurring_charge" value="yes">
            </div>
        </div>

    </div>
    @endforeach
    @endif
</div>


@include('admin.CRUD.wizardbuttons')
</form>
@elseif(($routeParts[1] === 'edit'))
<form method="POST" action="{{ url($routeParts[0].'/'.$unitcharge->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @method('PUT')    
        @csrf
    <h4 style="text-transform: capitalize;"> Edit Charge Details &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink">Edit</a>
        @endif
    </h4>
    <hr>
    <div class="col-md-5">
        <div class="form-group">
            <label class="label"> {{ $unitcharge->charge_name}} Cycle<span class="requiredlabel">*</span></label>
            <h5>
                <small class="text-muted">
                    {{ $rentcharge->charge_cycle}}
                </small>
            </h5>
            <select name="charge_cycle" id="charge_cycle" class="formcontrol2" placeholder="Select" required>
                <option value="{{$rentcharge->charge_cycle ?? ''}}">{{$rentcharge->charge_cycle ?? 'Select Rent Cycle'}}</option>
                <option value="Monthly"> Monthly</option>
                <option value="Twomonths">Two Months</option>
                <option value="Quaterly">Quaterly</option>
                <option value="Halfyear">6 Months</option>
                <option value="Year">1 Year</option>

            </select>
        </div>
    </div>
    <!---------   ---->
  
        <div class="col-md-5">
            <div class="form-group">
                <label class="label">Account<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $rentcharge->chartofaccounts->account_name}}
                    </small>
                </h5>
                <select name="chartofaccounts_id" id="chartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$rentcharge->chartofaccounts_id ?? ''}}">{{$rentcharge->chartofaccounts->account_name ?? 'Select Account'}}</option>
                    @foreach($accounts as $accounttype => $account)
                    <optgroup label="{{ $accounttype }}">
                        @foreach($account as $item)
                        <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div><br />
        <div class="col-md-5">
            <div class="form-group">
                <label class="label">Rate/Amount<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $unitcharge->rate}}
                    </small>
                </h5>
                <input type="text" class="form-control" id="rate" name="rate" value="{{$unitcharge->rate ?? ''}}">
               
            </div>
        </div>
    <hr>
    <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Utility Details</button>
        </div>


</form>

@endif

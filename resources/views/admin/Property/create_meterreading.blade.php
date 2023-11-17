@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Meter reading</h4>
    <hr>
    <form method="POST" action="{{ url('meter-reading') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Charge</label>
                <select name="unitcharge_id" id="unitcharge_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    @foreach($unitcharge as $item)
                    <option value="{{$item->id}}">{{$item->charge_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">


            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Previous Reading<span class="requiredlabel">*</span></label>
                    <input type="number" class="form-control" name="lastreading" id="lastreading" value="{{old('lastreading') ?? '0.00'}}" required {{ Auth::user()->id === 1 ||  Auth::user()->can($routeParts[0].'.edit') ? '' : 'readonly' }}>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Current Reading<span class="requiredlabel">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="currentreading" value="{{old('currentreading')}}" required>
                </div>
            </div>



            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Date of Last Reading<span class="requiredlabel">*</span></label>
                    <input type="text" class="form-control" name="startdate" id="startdate" value="{{old('startdate') ?? now()->toDateString()}}" required readonly>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">End Date of Reading Period<span class="requiredlabel">*</span></label>
                    <input type="date" class="form-control" name="enddate" value="{{old('enddate')}}" required>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Record Meter Reading</button>
        </div>

    </form>
</div>
@endsection
@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Over Ride Setting</h4>
    <hr>
    <form method="POST" action="{{ url('setting') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Module</label>
                <input type="text" class="form-control" name="module" value="{{$setting->module}}" required>
                <input type="text" class="form-control" name="name" value="{{$setting->name}}" required>
                <input type="text" class="form-control" name="model_type" value="{{$setting->model_type}}" required>


            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Module<span class="requiredlabel">*</span></label>
                <select class="formcontrol2" id="" name="model_id">
                    <option value="">Select Value</option>
                    @foreach ($options as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Model Type<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="setting_value" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Model id<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="setting_description" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Key<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="setting_description" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Value<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="setting_description" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Description<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="setting_description" value="" required>
            </div>
        </div>


        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Setting</button>
        </div>

    </form>
</div>
@endsection
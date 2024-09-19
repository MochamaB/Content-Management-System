@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Over Ride Setting</h4>
    <hr>
    <form method="POST" action="{{ url('setting') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                
                <input type="text" class="form-control" name="module" value="{{$setting->module}}" required>
                <input type="text" class="form-control" name="name" value="{{$setting->name}}" required>
                <input type="text" class="form-control" name="model_type" value="{{$setting->model_type}}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Item to be overridden<span class="requiredlabel">*</span></label>
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
                <label class="label">SETTING<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="key" value="{{$setting->key}}" required readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">VALUE<span class="requiredlabel">*</span></label>
                @if($setting->key === 'Mass Update Utilities')
                <select class="formcontrol2" id="" name="value">
                    <option value="">Select Value</option>
                    <option value="YES">YES</option>
                    <option value="NO">NO</option>
                </select>
                @else
                @endif
                <textarea class="form-control" id="" name="description" rows="3" columns= "5" hidden>
                    {{$setting->description}}
                </textarea>
            </div>
        </div>
   
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Setting</button>
        </div>

    </form>
</div>
@endsection
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
                <select class="formcontrol2" id="" name="model_id">
                    <option value="">Select Setting</option>
                    @foreach ($setting as $item)
                    <option value="{{ $item->key }}">{{ $item->key }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{{$modelClass}} Item to be overridden<span class="requiredlabel">*</span></label>
                <select class="formcontrol2" id="" name="model_id">
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
@endsection
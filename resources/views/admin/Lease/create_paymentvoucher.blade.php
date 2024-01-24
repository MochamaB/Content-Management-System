@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Payment Voucher</h4>
    <hr>
    <form method="POST" action="{{ url('paymentvoucher') }}" class="myForm" novalidate>
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
       
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment Voucher</button>
        </div>

    </form>
</div>
@endsection
@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">
    <h4>New Payment</h4>
    <hr>
    <form method="POST" action="{{ url('payment') }}" class="myForm" novalidate>
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
            <input type="hidden" class="form-control" name="model_type" id="model_type" value="{{$className}}">
            <input type="hidden" class="form-control" name="model_id" id="model_id" value="{{$invoice->id}}">
            <input type="hidden" class="form-control" name="referenceno" id="referenceno" value="{{$referenceno}}">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Type</label>
                <select name="payment_type_id" id="payment_type_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Payment Method</option>
                    @foreach($paymenttype as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Code</label>
                <input type="text" class="form-control" name="payment_code" id="payment_code" value="">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Amount</label>
                <input type="text" class="form-control" name="total_amount" id="total_amount" value="">
            </div>
        </div>


        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Record Meter Reading</button>
        </div>

    </form>

</div>

@endsection
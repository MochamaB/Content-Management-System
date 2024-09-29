@extends('layouts.admin.admin')

@section('content')

@php

@endphp

<div class=" contwrapper">
    <h5 style="text-transform: capitalize;">{{$routeParts[0]}} Details &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    </h5>
    @endif
    <hr>
    <form method="POST" action="{{ url('payment') }}" class="myForm" novalidate>
        @csrf
        <input type="hidden" name="instanceId" value="{{ $instance->id }}">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{$instance->property->property_name}}
                    </small>
                </h6>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$instance->property->id}}">{{$instance->property->property_name}}</option>
                </select>
            </div>
        </div>
        @if($instance->unit_id)
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{$instance->unit->unit_number}}
                    </small>
                </h6>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$instance->unit->id}}">{{$instance->unit->unit_number}}</option>
                </select>
            </div>
        </div>
        @endif

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Method<span class="requiredlabel">*</span></label>
                <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                    {{$instance->paymentMethod->name}}
                    </small>
                </h6>
                <select name="payment_method_id" id="payment_method_id" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$instance->payment_method_id}}">{{$instance->paymentMethod->name}}</option>
                    @foreach($PaymentMethod as $item)
                    <option value="{{$item->id}}">{{ucwords($item->name)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Payment Code<span class="requiredlabel">*</span></label>
                <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                    {{$instance->payment_code ?? 'NONE'}}
                    </small>
                </h6>
                <input type="text" class="form-control" name="payment_code" value=" {{$instance->payment_code}}">
              
            </div>
        </div>

        <!------- THIRD LEVEL INVOICE ITEMS -->



        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment</button>
        </div>

    </form>

</div>

@endsection
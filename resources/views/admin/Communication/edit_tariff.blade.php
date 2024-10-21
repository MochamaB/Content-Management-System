@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">

@if( $routeParts[1] === 'edit' && Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <h5>Edit Tariff
        <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    </h5>
    @endif
    <hr>

    <form method="POST" action="{{ url('smsCredit/'.$smsCredit->id) }}" class="myForm" novalidate>
        @csrf
        @method('PUT')
    <div class="col-md-6">
        <div class="form-group">
        <label for="credit_type">Credit Type</label>
        <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                {{ \App\Models\smsCredit::$statusLabels[$smsCredit->credit_type] }}
                </small>
            </h6>
            <select name="credit_type" id="credit_type" class="formcontrol2" placeholder="Select" required readonly>
                <option value="{{ $smsCredit->credit_type }}">{{ \App\Models\smsCredit::$statusLabels[$smsCredit->credit_type] }}</option>
    
            </select>
        </div>

       @if($smsCredit->property)
        <div class="form-group" id = 'property'>
            <label class="label">Property <span class="requiredlabel">*</span></label>
            <small class="text-muted" style="text-transform: capitalize;">
                {{$smsCredit->property->property_name}}
                </small>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select"  required readonly>
                    <option value="{{$smsCredit->property_id}}">{{$smsCredit->property->property_name}}</option>
            </select>
        </div>
        @endif
        
        @if($smsCredit->property)
            <div class="form-group" id ="user">
                <label class="label">User <span class="requiredlabel">*</span></label>
                <small class="text-muted" style="text-transform: capitalize;">
                {{$smsCredit->user->firstname}} {{$smsCredit->user->lastname}}
                </small>
                <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select"  required readonly >
                <option value="{{$smsCredit->user_id}}">{{$smsCredit->user->firstname}} {{$smsCredit->user->lastname}}</option>
              
            </select>
                
            </div>
            @endif
       
            <div class="form-group" id="tariff">
                <label class="label">Tariff Rate <span class="requiredlabel">*</span></label>
                </br>
                <small class="text-muted" style="text-transform: capitalize;">
                {{ $smsCredit->tariff }}
                </small>
                <input type="number" class="form-control" id="tariff" name="tariff" value="{{$smsCredit->tariff}}" required >
            </div>       
        <div class="col-md-6" id="submitBtn">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Edit Tariff</button>
        </div>
    </div>
    </form>
</div>





@endsection
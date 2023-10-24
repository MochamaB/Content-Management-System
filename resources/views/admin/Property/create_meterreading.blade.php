@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

        <h4>New Meter reading</h4>
        <hr>
    <form method="POST" action="{{ url('property/create') }}" class="myForm"  novalidate >
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id ="property_id" class="formcontrol2" placeholder="Select" required disabled>
                            <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>                                          
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id ="unit_id" class="formcontrol2" placeholder="Select" required disabled>
                            <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>                                          
            </div>
        </div>



    </form>
</div>
@endsection
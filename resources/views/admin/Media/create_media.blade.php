@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4>New Media File</h4>
    <hr>
    <form method="POST" action="{{ url('media') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        @if($model)
        <input type="hidden" name="id" value="{{ $id }}">
        <input type="hidden" name="model" value="{{ $model }}">
        @endif
       
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Gategory Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
    
        @if($unit)
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>
            </div>
        </div>
        @endif
       
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Type of Media</label>
                <select name="collection_name" id="collection_name" class="formcontrol2" placeholder="Select" required>
                <option value="">Select Media Type</option>    
                <option value="picture">Picture</option>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="document">Text Document</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Upload Media<span class="requiredlabel">*</span></label>
                <input type="file" name="media" class="form-control" id="logo" required/>
                <img id="logo-image-before-upload" src="{{ url('resources/uploads/images/noimage.jpg') }}" style="height: 200px; width: 200px;">
            </div>
        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Media File</button>
        </div>


    </form>
</div>
@endsection
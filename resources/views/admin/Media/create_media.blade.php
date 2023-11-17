@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4>New Media File</h4>
    <hr>
    <form method="POST" action="{{ url('media') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Title<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="name" id="name" value="{{old('name') ?? ''}}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Upload File<span class="requiredlabel">*</span></label>
                <input type="file" name="file" class="form-control" id="logo" required/>
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
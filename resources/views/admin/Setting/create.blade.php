@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Global Setting</h4>
    <hr>
    <form method="POST" action="{{ url('setting') }}" class="myForm" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Model Name</label>
                <input type="text" class="form-control" name="settingable_type" id="settingable_type" value="{{$model}}" readonly required >
                <input type="hidden" class="form-control" name="settingable_id" id="settingable_id" value="{{$id}}" readonly required >
            </div>
        </div>


                
          
       

            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Setting Name<span class="requiredlabel">*</span></label>
                    <input type="text" class="form-control" name="setting_name" id="setting_name" value=""  required >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Setting Value<span class="requiredlabel">*</span></label>
                    <input type="text"  class="form-control" name="setting_value" value="" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Setting Description<span class="requiredlabel">*</span></label>
                    <input type="text"  class="form-control" name="setting_description" value="" required>
                </div>
            </div>


            <hr>
    <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add {{$model}} Setting</button>
        </div>

    </form>
</div>
@endsection
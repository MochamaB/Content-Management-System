@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h5>New Slider </h5>
    <hr>
    <form method="POST" action="{{ url('slider') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name<span class="requiredlabel">*</span></label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    @foreach($properties as $property)
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Title <span class="requiredlabel">*</span></label>
                <input type="text" name="slider_title" id="slider_title" class="form-control" value="{{old('slider_title')}}" required />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Slider Picture</label>
                <input type="file" name="slider_picture" value="" class="form-control" id="logo" />
                <img id="logo-image-before-upload" src="{{ url('uploads/noimage.jpg') }}" alt="No Image" style="height: 200px; width: 250px;">
            </div>
        </div>
        <div class="col-md-8">        
                <div class="form-group">
                    <label class="label"> Description<span class="requiredlabel">*</span></label>
                    <textarea class="form-control" style=" width: 100%;padding:5px;" id="" name="slider_desc">
                    {{old('slider_desc')}}
                    </textarea>
                </div>
        </div>
      
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Slider</button>
        </div>

    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the dropdown and text input elements
        const propertyDropdown = document.getElementById('property_id');
        const sliderTitleInput = document.getElementById('slider_title');

        // Listen for changes on the dropdown
        propertyDropdown.addEventListener('change', function () {
            // Get the selected option's text
            const selectedOptionText = propertyDropdown.options[propertyDropdown.selectedIndex].text;

            // Append the value of property_name to the slider_title input
            if (selectedOptionText !== "Select Value") {
                sliderTitleInput.value = selectedOptionText;
            } else {
                sliderTitleInput.value = ""; // Clear input if "Select Value" is chosen
            }
        });
    });
</script>




@endsection
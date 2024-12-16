@if(($routeParts[1] === 'create'))
<h5><b> Unit Amenities</b></h5>
<hr>
<form method="POST" action="{{ url('unitamenities') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="form-group">
        <label>Select Amenities:</label>
        @foreach($amenities as $amenity)

        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="amenities[]" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" checked="">
                {{ $amenity->amenity_name }}
                <i class="input-helper"></i></label>
        </div>
        @endforeach
    </div>
    @include('admin.CRUD.wizardbuttons')
</form>

@else
<h5 style="text-transform: capitalize;">Unit Amenities &nbsp;
    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    @endif
</h5>
<hr>
<form method="POST" action="{{ route('listing.update', $listing->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="form-group">
        <label class="label">Select Amenities:</label>
        @foreach ($allAmenities as $amenity)
        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input"
                       name="amenities[]"
                       id="amenity_{{ $amenity->id }}"
                       value="{{ $amenity->id }}"
                       {{ in_array($amenity->id, $currentAmenities) ? 'checked' : '' }}>
                {{ $amenity->amenity_name }}
                <i class="input-helper"></i>
            </label>
        </div>
    @endforeach
    </div>
    <hr>
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Edit Amenities</button>
    </div>
  
</form>
@endif
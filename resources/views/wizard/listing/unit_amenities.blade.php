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

@elseif(($routeParts[1] === 'edit'))
@endif
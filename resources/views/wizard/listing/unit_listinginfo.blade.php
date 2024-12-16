@if(($routeParts[1] === 'create'))
<h5><b> Listing Info</b></h5>
<hr>
<form method="POST" action="{{ url('unitListingInfo') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Agent / Contact Person <span class="requiredlabel">*</span></label>
            <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select" required>
                <option value=""> Select Agent</option>
                @foreach($users as  $item)
                <option value="{{ $item->id }}">{{ $item->firstname }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-8">
            <div class="form-group">
                <label class="label">Unit Size<span class="requiredlabel">*</span></label>
                <div class="input-group">
                    <span class="input-group-text spanmoney">Sqm</span>
                    <input type="number" class="form-control" name="size" placeholder="Enter Area of Unit" value="{{ old('size') }}" required>
                </div>
            </div>
        </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Title<span class="requiredlabel">*</span></label>
            <input type="text" class="form-control " name="title" value="{{ old('title') ?? 'Spacious Unit Available' }}" required>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label for="description">Description<span class="requiredlabel">*</span> </label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>
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
    
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Agent / Contact Person <span class="requiredlabel">*</span></label>
            <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{$listing->user->firstname ?? ''}} {{$listing->user->lastname ?? ''}}
                </small>
            </h6>
            <select name="user_id" id="user_id" class="formcontrol2" placeholder="Select" required>
                <option value="{{$listing->user->id ?? ''}}"> {{$listing->user->firstname ?? ''}} {{$listing->user->lastname ?? ''}}</option>
                @foreach($users as  $item)
                <option value="{{ $item->id }}">{{ $item->firstname }} {{ $item->lastname }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-8">
            <div class="form-group">
                <label class="label">Unit Size<span class="requiredlabel">*</span></label>
                <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{$listing->size ?? ''}} Sqm<sup>2</sup>
                </small>
            </h6>
                <div class="input-group">
                    <span class="input-group-text spanmoney">Sqm<sup>2</sup></span>
                    <input type="number" class="form-control" name="size" placeholder="Enter Area of Unit" value="{{ $listing->size ?? old('size') }}" required>
                </div>
            </div>
        </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Title<span class="requiredlabel">*</span></label>
            <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{$listing->title ?? ''}}
                </small>
            </h6>
            <input type="text" class="form-control " name="title" value="{{ $listing->title ?? old('title') ?? 'Spacious Unit Available' }}" required>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label for="description">Description<span class="requiredlabel">*</span> </label>
            <h6>
                <small class="text-muted" style="text-transform: capitalize;">
                    {{$listing->description ?? ''}}
                </small>
            </h6>
            <textarea name="description" id="description" class="form-control">{{$listing->description ?? old('description') }}</textarea>
        </div>
    </div>
    <hr>
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Edit Amenities</button>
    </div>
  
</form>
@endif
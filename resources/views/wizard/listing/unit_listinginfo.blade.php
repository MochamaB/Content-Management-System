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

@elseif(($routeParts[1] === 'edit'))
@endif
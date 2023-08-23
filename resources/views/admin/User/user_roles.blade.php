<!------------ Create------------------->
@if(($routeParts[1] === 'create'))

<h4>Select Role</h4>
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Role Name<span class="requiredlabel">*</span></label>
        <select name="role" id="role" class="formcontrol2" required>
            <option value="">Select A Role</option>
            @foreach($roles as $item)
            <option value="{{$item->id}}">{{$item->name}}</option>
            @endforeach
            <select>
    </div>
    <br />

</div>
<div class="col-md-4">
    <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0 nextBtn" id="nextBtn">Next:Contact Information</button>
</div>

@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">{{$routeParts[0]}} Role &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
<div class="col-md-6">
    <div class="form-group">
        <label class="label">Role Name<span class="requiredlabel">*</span></label>
        <h5>
                    <small class="text-muted">
                    {{ $user->roles->first()->name ?? '' }}
                    </small>
                </h5>
        <select name="role" id="role" class="formcontrol2" required>
            <option value=" {{ $user->roles->first()->id  ?? ''}}"> {{ $user->roles->first()->name ?? '' }}</option>
            @foreach($roles as $item)
            <option value="{{$item->id ?? ''}}">{{$item->name ?? ''}}</option>
            @endforeach
            <select>
    </div>
</div>
<br />
<div class="col-md-5">
                <div class="row">
                    <div class="col-md-6 ">
                        <button type="submit" class="btn btn-primary  btn-lg text-white mb-0 me-0 submitBtn" id="">Edit: User Role</button>
                    </div>
                </div>
            </div>
@endif

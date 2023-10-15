    @if(($routeParts[1] === 'create'))
    <form method="POST" action="{{ url('role') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <h4> Roles Summary</h4>
        <hr>
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Role Name<span class="requiredlabel">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{$role->name ?? old('name') }}" required />
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="label">Role Description<span class="requiredlabel">*</span></label>
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="description" name="description" required>
                {{$role->description ?? ""}}
                </textarea>
            </div>
        </div>
        @include('admin.CRUD.wizardbuttons')
        
    </form>
        @elseif(($routeParts[1] === 'edit'))
        <h4 style="text-transform: capitalize;"> Role Summary &nbsp;
            @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
            <a href="" class="editLink">Edit</a>
        </h4>
        @endif
        <hr>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Role Name<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $role->name }}
                    </small>
                </h5>
                <input type="text" name="name" id="name" class="form-control" value=" {{ $role->name }}" required />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Role Description<span class="requiredlabel">*</span></label>
                <h5>
                    <small class="text-muted">
                        {{ $role->description }}
                    </small>
                </h5>
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="description" name="description" required>
                {{ $role->description }}
                </textarea>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-6 ">
                    <button type="submit" class="btn btn-primary  btn-lg text-white mb-0 me-0 submitBtn" id="">Edit: Role</button>
                </div>
            </div>
        </div>



        @endif
@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">

    @if(($routeParts[1] === 'create'))
    <form method="POST" action="{{ url($routeParts[0]) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf

        <h4>New Permission</h4>
        <hr>
        <div class="col-md-4">
                <div class="form-group">
                <label class="label">Module Name<span class="requiredlabel">*</span></label>
                    <input class="form-control" list="moduleList" id="module" name="module" required>
                    <datalist id="moduleList">
                        @foreach ($module as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </datalist>
                </div>
        </div>
        <div class="col-md-4">
                <div class="form-group">
                <label class="label">Sub Module Name<span class="requiredlabel">*</span></label>
                    <input class="form-control" list="submoduleList" id="submodule" name="submodule" required>
                    <datalist id="submoduleList">
                        @foreach ($submodule as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </datalist>
                </div>
        </div>
        <div class="col-md-4">
                <div class="form-group">
                <label class="label">Action<span class="requiredlabel">*</span></label>
                    <input class="form-control" list="actionList" id="action" name="action" required>
                    <datalist id="actionList">
                        @foreach ($action as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </datalist>
                </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Permission Name<span class="requiredlabel">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submit">Create Permission</button>
        </div>


    </form>
    @elseif(($routeParts[1] === 'edit'))
    <form method="POST" action="{{ url($routeParts[0].'/'.$permission->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        
        <h4>Permissions 
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
            <a href="#" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a></h4>
        @endif
        <hr>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Module Name<span class="requiredlabel">*</span></label>
                <h5><small class="text-muted">
                        {{ $permission->module }}
                    </small> </h5>
                <select class="formcontrol2" id="" name="module" value="{{ $permission->module }}">
                <option value="{{ $permission->module }}">{{ $permission->module }}</option>
                    @foreach ($module as $key => $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Sub Module Name<span class="requiredlabel">*</span></label>
                <h5><small class="text-muted">
                        {{ $permission->submodule }}
                    </small> </h5>
                <select class="formcontrol2" id="" name="submodule" value="{{ $permission->submodule }}">
                <option value="{{ $permission->submodule }}">{{ $permission->submodule }}</option>
                    @foreach ($submodule as $key => $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Action<span class="requiredlabel">*</span></label>
                <h5><small class="text-muted">
                        {{ $permission->action }}
                    </small> </h5>
                <select class="formcontrol2" id="" name="action" value="{{ $permission->action }}">
                <option value="{{ $permission->action }}">{{ $permission->action }}</option>
                    @foreach ($action as $key => $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label class="label">Permission Name<span class="requiredlabel">*</span></label>
                <h5><small class="text-muted">
                        {{ $permission->name }}
                    </small> </h5>
                <input type="text" name="name" id="name" class="form-control" value="{{ $permission->name }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submit">Edit Permission</button>
        </div>

    </form>
    @endif



</div>
@endsection
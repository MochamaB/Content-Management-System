@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('assignpermission') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
<h6>Menu/Module Access</h6>
<hr>
<!-- Menu Access content -->
<div id="accordion">
    @foreach ($groupedPermissions->sortBy->keys() as $module => $modulePermissions)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{$module}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $module}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <p>{{ $module}} Menu ({{ $modulePermissions->flatten()->count() }})</p>
            <div class="form-check form-check-inline ">
                <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                <label class="checkboxlabel pt-0 m-0" for="">
                    Select All
                </label>
            </div>
        </div>

        <div id="{{ $module}}Collapse" class="collapse hide" aria-labelledby="{{$module}}" data-parent="#accordion">
            <div class="card-body">
                @foreach($modulePermissions as $submoduleName => $submodulePermissions)
                <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="d-flex justify-content-between align-items-center">
                    @foreach ($submodulePermissions as $permission)
                    <div class="form-check form-check-inline align-items-center">
                        <input class="form-check-input p-0 body-checkbox" type="checkbox" name="permission[{{$permission->name}}]" value="{{$permission->name}}" id=""
                        {{ in_array($permission->name, $rolePermissions) 
                                                    ? 'checked'
                                                    : '' }}>
                        <label class="checkboxlabelbody pt-0 m-0" for="">
                        @if( $permission->action ==="index")
                                View
                            @else
                            {{ $permission->action }}
                            @endif
                        </label>
                    </div>
                    @endforeach
                    <div class="" style="width: 200px;">
                        <span style="color: blue;text-transform:capitalize">{{$submoduleName}}</span>
                    </div>
                </div>

                @endforeach

            </div>
        </div>
    </div>
    @endforeach
</div><br /><br />
@include('admin.CRUD.wizardbuttons')
</form>
@elseif(($routeParts[1] === 'edit'))
<h5 style="text-transform: capitalize;"> Menu/Module Access &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a></h5>
@endif
<hr>
<div id="accordion">
    @foreach ($groupedPermissions->sortBy->keys() as $module => $modulePermissions)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{$module}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $module}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <h6>{{ $module}} Menu ({{ $modulePermissions->flatten()->filter(function ($permission) use ($rolePermissions) {
            return in_array($permission->name, $rolePermissions);
        })->count() }} / {{ $modulePermissions->flatten()->count() }})</h6>
      
            <div class="form-check form-check-inline ">
                <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                <label class="checkboxlabel pt-0 m-0" for="">
                    Select All
                </label>
            </div>
        </div>

        <div id="{{ $module}}Collapse" class="collapse hide" aria-labelledby="{{$module}}" data-parent="#accordion">
            <div class="card-body">
                @foreach($modulePermissions as $submoduleName => $submodulePermissions)
                <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="d-flex justify-content-between align-items-center">
                    @foreach ($submodulePermissions as $permission)
                    <div class="form-check form-check-inline align-items-center">
                        <input class="form-check-input p-0 body-checkbox" type="checkbox" name="permission[{{$permission->name}}]" value="{{$permission->name}}" id=""
                        {{ in_array($permission->name, $rolePermissions) 
                                                    ? 'checked'
                                                    : '' }}>
                                                            
                        <label class="checkboxlabelbody pt-0 m-0" for="">
                            @if( $permission->action ==="index")
                                View
                            @else
                            {{ $permission->action }}
                            @endif
                        </label>
                    </div>
                    @endforeach
                    <div class="" style="width: 200px;">
                        <span style="color: blue;text-transform:capitalize">{{$submoduleName}}</span>
                    </div>
                </div>

                @endforeach

            </div>
        </div>
    </div>
    @endforeach
</div><br /><br />
<div class="col-md-6">
          <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Module Access</button>
        </div>


@endif
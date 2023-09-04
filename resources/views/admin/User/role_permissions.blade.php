@if(($routeParts[1] === 'create'))

<h4>Menu/Module Access</h4>
<hr>
<!-- Menu Access content -->
<div id="accordion">
    @foreach ($groupedPermissions->sortBy->keys() as $module => $modulePermissions)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{$module}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $module}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <h4>{{ $module}} ({{ $modulePermissions->flatten()->count() }})</h4>
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
                        <input class="form-check-input p-0 body-checkbox" type="checkbox" name="permission[{{$permission->name}}]" value="{{$permission->name}}" id="">
                        <label class="checkboxlabelbody pt-0 m-0" for="">
                            {{ $permission->action }}
                        </label>
                    </div>
                    @endforeach
                    <div class="" style="width: 120px;">
                        <span style="color: blue;text-transform:capitalize">{{$submoduleName}}</span>
                    </div>
                </div>

                @endforeach

            </div>
        </div>
    </div>
    @endforeach
</div><br /><br />
<div class="col-md-5">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous:Roles</button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0 nextBtn" id="nextBtn">Next:Report Access</button>
        </div>
    </div>
</div>

@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;"> Menu/Module Access &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
<div id="accordion">
    @foreach ($groupedPermissions->sortBy->keys() as $module => $modulePermissions)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{$module}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $module}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <h4>{{ $module}} ({{ $modulePermissions->flatten()->filter(function ($permission) use ($rolePermissions) {
            return in_array($permission->name, $rolePermissions);
        })->count() }} / {{ $modulePermissions->flatten()->count() }})</h4>
      
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
                            {{ $permission->action }}
                        </label>
                    </div>
                    @endforeach
                    <div class="" style="width: 120px;">
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
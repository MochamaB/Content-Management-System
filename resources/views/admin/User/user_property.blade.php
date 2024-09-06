@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('assignProperties') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <h5>Property Access</h5>
    <hr>
    <div id="accordion" class="propertyaccess">
        @if(stripos($savedRole, 'Tenant') !== false)
        @include('layouts.admin.nodata', ['message' => 'You have to create a Lease to assign units to Tenants'])
        @else

        @if ($propertyaccess->isEmpty())
        @include('layouts.admin.nodata', ['message' => 'No accessible units available. Contact Admin'])
        @endif

        @foreach ($propertyaccess as $property)

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" id="{{ $property->id}}">
                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $property->id}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                    <i class="menu-icon mdi mdi-plus"></i>
                </button>
                <h6>{{ $property->property_name}}</h6>
                <div class="form-check form-check-inline ">
                    <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                    <label class="checkboxlabel pt-0 m-0" for="">
                        Select All
                    </label>
                </div>
            </div>

            <div id="{{ $property->id}}Collapse" class="collapse hide" aria-labelledby="{{ $property->id}}" data-parent="#accordion">
                <div class="card-body">
                    @foreach ($property->units as $unit)
                    <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="col-md-4">

                        <div class="form-check form-check-inline align-items-center">
                            <input class="form-check-input p-0 body-checkbox" type="checkbox" name="unit_id[{{ $unit->id }}]" value="{{ $unit->id }}" id="" {{ in_array($unit->id, $assignedUnits) 
                                                    ? 'disabled'
                                                    : '' }}>
                            <input type="hidden" name="property_id[{{ $unit->id }}]" value="{{ $unit->property_id }}">
                            <label class="checkboxlabelbody pt-0 m-0" for="">
                                {{ $unit->unit_number }}
                            </label>
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div><br /><br />

    @include('admin.CRUD.wizardbuttons')
</form>

@elseif(($routeParts[1] === 'edit'))
<form method="POST" action="{{ url('updateAssignedUnits/'.$editUser->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    <h5 style="text-transform: capitalize;">{{$routeParts[0]}} Property Acess &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink">Edit</a>
    </h5>
    @endif
    <hr>
    @if(stripos($userRole, 'Tenant') !== false)
    @include('layouts.admin.nodata', ['message' => 'You have to create or delete a lease to assign or remove units to Tenants'])
    @else

    <div id="accordion" class="propertyacess">

        @if ($propertyaccess->isEmpty())
        @include('layouts.admin.nodata', ['message' => 'No accessible units available. Contact Admin'])
        @endif

        @foreach ($propertyaccess as $key => $property)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" id="{{ $property->id}}">
                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $property->id}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                    <i class="menu-icon mdi mdi-plus"></i>
                </button>
                <h6>{{ $property->property_name}}</h6>
                <div class="form-check form-check-inline ">
                    <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                    <label class="checkboxlabel pt-0 m-0" for="">
                        Select All
                    </label>
                </div>


            </div>

            <div id="{{ $property->id}}Collapse" class="collapse hide" aria-labelledby="{{ $property->id}}" data-parent="#accordion">
                <div class="card-body">
                    @foreach ($property->units as $unit)
                    <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="col-md-4">

                        <div class="form-check form-check-inline align-items-center">
                            <input class="form-check-input p-0 body-checkbox" type="checkbox" name="unit_id[{{ $unit->id }}]" value="{{ $unit->id }}" id="" {{ in_array($unit->id, $assignedproperties) ? 
                            'checked' : (in_array($unit->id, $assignedUnits) ? 'disabled' : '') }}>
                            <input type="hidden" name="property_id[{{ $unit->id }}]" value="{{ $unit->property_id }}">
                            <label class="checkboxlabelbody pt-0 m-0" for="">
                                {{ $unit->unit_number }}
                            </label>
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        @endforeach
    </div><br /><br />



    <div class="col-md-6">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Edit Property Acess</button>
    </div>
</form>
@endif


@endif
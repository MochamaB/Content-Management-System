
@if(($routeParts[1] === 'create'))

<h4>Property Access</h4>
<hr>
<div id="accordion">
    @foreach ($propertyaccess as $propertyId  => $unitList)
    @php
        $property = $unitList->first()->property;
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{ $property->id}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $property->id}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <h4>{{ $property->property_name}}</h4>
            <div class="form-check form-check-inline ">
                <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                <label class="checkboxlabel pt-0 m-0" for="">
                    Select All
                </label>
            </div>


        </div>

        <div id="{{ $property->id}}Collapse" class="collapse hide" aria-labelledby="{{ $property->id}}" data-parent="#accordion">
            <div class="card-body">
            @foreach ($unitList as $unit)
                <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="col-md-4">
                
                    <div class="form-check form-check-inline align-items-center">
                        <input class="form-check-input p-0 body-checkbox" type="checkbox" name="unit_id[{{ $unit->id }}]" value="{{ $unit->id }}" id="">
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
<div class="col-md-5">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous: Logins</button>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 " id="">Create New User</button>
        </div>
    </div>
</div>
@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">{{$routeParts[0]}} Property Acess &nbsp; 
@if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
<a href="" class="editLink">Edit</a></h4>
@endif
<hr>
<div id="accordion">

    @if ($propertyaccess->isEmpty())
    @include('layouts.admin.nodata', ['message' => 'No accessible units available. Contact Admin'])
    @endif
    
    @foreach ($propertyaccess as $propertyId  => $unitList)
    @php
        $property = $unitList->first()->property;
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" id="{{ $property->id}}">
            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#{{ $property->id}}Collapse" aria-expanded="true" aria-controls="collapseOne">
                <i class="menu-icon mdi mdi-plus"></i>
            </button>
            <h4>{{ $property->property_name}}</h4>
            <div class="form-check form-check-inline ">
                <input class="form-check-input p-0 header-checkbox" type="checkbox" name="header-checkbox" id="header-checkbox">
                <label class="checkboxlabel pt-0 m-0" for="">
                    Select All
                </label>
            </div>


        </div>

        <div id="{{ $property->id}}Collapse" class="collapse hide" aria-labelledby="{{ $property->id}}" data-parent="#accordion">
            <div class="card-body">
            @foreach ($unitList as $unit)
                <div style="padding:7px 5px 7px 40px; border-bottom:2px solid #ced4da" class="col-md-4">
                
                    <div class="form-check form-check-inline align-items-center">
                        <input class="form-check-input p-0 body-checkbox" type="checkbox" name="unit_id[{{ $unit->id }}]" value="{{ $unit->id }}" id=""
                        {{ in_array($unit->id, $assignedproperties) 
                                                    ? 'checked'
                                                    : '' }}>
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
 


@endif
@extends('layouts.admin.admin')

@section('content')
@include('admin.CRUD.timeline_horizontal')
<div class="row">
    <div class="col-md-7">

        <div class=" contwrapper">

            <h5 style="text-transform: capitalize;">{{$routeParts[0]}} Details &nbsp;
            </h5>
            <hr>
            <div class="col-md-6">
                <div class="form-group">
                    @foreach($fields as $field => $attributes)
                    <!--- LABEL --><br />
                    <label class="label">{{ $attributes['label'] }}
                        @if ($attributes['required'])
                        <span class="requiredlabel">*</span>
                        @endif
                    </label>
                    <h6>
                        <small class="text-muted" style="text-transform: capitalize;">
                            @if($specialvalue !== null && $specialvalue->has($field))
                            {{ $specialvalue[$field] }}
                            @else
                            {{ $actualvalues->$field }}
                            @endif
                        </small>
                    </h6>
                @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5" >
        <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">

            <h5><b> </b>
            </h5>
            <hr>
        


        </div>

    </div>
</div>

@endsection
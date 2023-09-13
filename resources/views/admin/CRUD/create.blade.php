<div class=" contwrapper">

    <h4><b> New {{ $routeParts[0] }}</b></h4>
    <hr>
    <form method="POST" action="{{ url($routeParts[0]) }}" id="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="model_name" value="{{ ucfirst($routeParts[0]) }}">
        @foreach($fields as $field => $attributes)
        <div class="col-md-5">
            <div class="form-group">
                <!--- LABEL -->
                <label class="label">{{ $attributes['label'] }}
                    @if ($attributes['required'])
                    <span class="requiredlabel">*</span>
                    @endif
                </label>
                <!---- NORMAL SELECT ------------->
                @if($attributes['inputType'] === 'select')
                <select class="formcontrol2" id="{{ $field }}" name="{{ $field }}">
                    @foreach ($data[$field] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <!---- GROUP SELECT ------------->
                @elseif($attributes['inputType'] === 'selectgroup')
                <select class="formcontrol2" id="{{ $field }}" name="{{ $field }}">
                    @foreach ($data[$field] as $groupLabel => $options)
                    <optgroup label="{{ $groupLabel }}">
                        @foreach ($options as $id => $option)
                        <option value="{{ $id }}">{{ $option }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                <!---- PICTURE INPUT ------------->
                @elseif($attributes['inputType'] === 'picture')

                <input type="file" name="{{ $field }}" class="form-control" id="logo" name="{{ $field }}" @if($attributes['required']) required @endif />
                <img id="logo-image-before-upload" src="{{ url('resources/uploads/images/noimage.jpg') }}" style="height: 200px; width: 200px;">
                <!---- NUMBER INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>

               
                </textarea>
                <br />

                <!---- EMAIL INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>
                <!---- NORMAL INPUT ------------->

                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>
                @endif



            </div>
        </div>
        @endforeach
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submit">Create {{$routeParts[0]}}</button>
        </div>
    </form>

</div>

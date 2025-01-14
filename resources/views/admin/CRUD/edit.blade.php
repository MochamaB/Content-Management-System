<form method="POST" action="{{ url($routeParts[0].'/'.$actualvalues->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <h5 style="text-transform: capitalize;">{{$routeParts[0]}} Details &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    </h5>
    @endif
    <hr>
    <div class="row">
        <!-- Left Column: Other Inputs -->
        <div class="col-md-6">

            @foreach($fields as $field => $attributes)
            <div class="form-group">
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
                <!---- NORMAL SELECT ------------->
                @if($attributes['inputType'] === 'select')
                <select class="formcontrol2" id="{{ $field }}" name="{{ $field }}">
                    <option value="{{ $actualvalues->$field }}">
                        @if($specialvalue !== null && $specialvalue->has($field))
                        {{ $specialvalue[$field] }}
                        @else
                        {{ $actualvalues->$field }}
                        @endif
                    </option>
                    @foreach ($data[$field] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <!---- GROUP SELECT ------------->
                @elseif($attributes['inputType'] === 'selectgroup')
                <select class="formcontrol2" id="{{ $field }}" name="{{ $field }}">
                    <option value="{{ $actualvalues->$field }}">
                        @if($specialvalue !== null && $specialvalue->has($field))
                        {{ $specialvalue[$field] }}
                        @else
                        {{ $actualvalues->$field }}
                        @endif
                    </option>
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

                <input type="file" name="{{ $field }}" class="form-control" id="logo" value="{{ $actualvalues->$field }}" name="{{ $field }}" />
                @if ($actualvalues)
                <img src="{{ $actualvalues->getFirstMediaUrl($mediaCollection ?? '', 'thumb')}}" alt="thumb" style="height: 200px; width: 300px;">
                @else
                <img src="url('uploads/images/noimage.jpg')" alt="No Image">
                @endif
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>

                {{$actualvalues->$field}}
                </textarea>
                <!---- NUMBER INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" value="{{ $actualvalues->$field }}" name="{{ $field }}" @if($attributes['required']) required @endif>
                <!---- NORMAL INPUT ------------->
                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" value="{{ $actualvalues->$field }}" name="{{ $field }}" @if($attributes['required']) required @endif @if($attributes['readonly']) readonly @endif>
                @endif
            </div>
            @endforeach


        </div>
        @if($usesMedia)
        <div class="col-md-6" style="border-left: 2px solid #dee2e6;">
            @include('admin.CRUD.upload_media')
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit {{$routeParts[0]}}</button>
    </div>


</form>


<script>
    $(document).ready(function() {
        $('.myForm').on("submit", function(event) {
            const $form = $(this);
            const $requiredFields = $form.find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').show();
                    $field.after('<div class="invalid-feedback">Please fill in this field.</div>');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                    $field.siblings('.invalid-feedback').hide();
                }
            });

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>
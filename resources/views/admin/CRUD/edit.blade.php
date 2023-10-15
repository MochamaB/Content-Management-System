
<form method="POST" action="{{ url($routeParts[0].'/'.$actualvalues->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

        <h4 style="text-transform: capitalize;">{{$routeParts[0]}} Details &nbsp; 
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
            <a href=""  class="editLink">Edit</a></h4>
        @endif
        <hr>

        <div class="col-md-4">
            <div class="form-group">
                @foreach($fields as $field => $attributes)
                <!--- LABEL -->
                <label class="label">{{ $attributes['label'] }}
                    @if ($attributes['required'])
                    <span class="requiredlabel">*</span>
                    @endif
                </label>
                <h5>
                    <small class="text-muted">
                        @if($specialvalue !== null && $specialvalue->has($field))
                        {{ $specialvalue[$field] }}
                        @else
                        {{ $actualvalues->$field }}
                        @endif
                    </small>
                </h5>
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
                <option value="{{ $actualvalues->$field }}">{{ $actualvalues->$field }}</option>
                    @foreach ($data[$field] as $groupLabel => $options)
                    <optgroup label="{{ $groupLabel }}">
                        @foreach ($options as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                <!---- PICTURE INPUT ------------->
                @elseif($attributes['inputType'] === 'picture')

                <input type="file" name="{{ $field }}" class="form-control" id="logo" value="{{ $actualvalues->$field }}" name="{{ $field }}" />
                <img id="logo-image-before-upload" src="{{ asset('resources/uploads/images/'.$actualvalues->$field) }}" style="height: 200px; width: 300px;">
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>

                {{$actualvalues->$field}}
                </textarea>
                <br/>
                 <!---- NUMBER INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                    <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" value="{{ $actualvalues->$field }}" name="{{ $field }}" @if($attributes['required']) required @endif >
                <!---- NORMAL INPUT ------------->
                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control" id="{{ $field }}" value="{{ $actualvalues->$field }}" name="{{ $field }}" 
                @if($attributes['required']) required @endif  @if($attributes['readonly']) readonly @endif>
                @endif

                @endforeach

            </div>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" id="submitBtn">Edit {{$routeParts[0]}}</button>
        </div>


</form>


<script>
    $(document).ready(function () {
    $('.myForm').on("submit", function (event) {
        const $form = $(this);
        const $requiredFields = $form.find('[required]');
        let isValid = true;

        $requiredFields.each(function () {
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

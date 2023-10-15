

    <h4 style="text-transform: capitalize;"><b> New {{ $routeParts[0] }}</b></h4>
    <hr>
    <form method="POST" action="{{ url($routeParts[0]) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="model_name" value="{{ ucfirst($routeParts[0]) }}">
        @foreach($fields as $field => $attributes)
        <div class="col-md-6">
            <div class="form-group">
                <!--- LABEL -->
                <label class="label" id="label-{{ $field }}">{{ $attributes['label'] }}
                    @if ($attributes['required'])
                    <span class="requiredlabel">*</span>
                    @endif
                </label>
                <!---- NORMAL SELECT ------------->
                @if($attributes['inputType'] === 'select')
                <select class="formcontrol2 @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}">
                <option value=""> Select Value'</option>
                    @foreach ($data[$field] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <!---- GROUP SELECT ------------->
                @elseif($attributes['inputType'] === 'selectgroup')
                <select class="formcontrol2 @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}">
                <option value=""> Select Value</option>
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
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif>
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" style=" width: 100%;padding:  1px 10px 75px 5px;" id="{{ $field }}" name="{{ $field }}" @if($attributes['required']) required @endif>

               
                </textarea>
                <br />

                <!---- EMAIL INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid  @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif>
                <!---- NORMAL INPUT ------------->

                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid  @enderror" id="{{ $field }}" name="{{ $field }}"  value="{{ old($field) }}" @if($attributes['required']) required @endif>
                @endif



            </div>
        </div>
        @endforeach
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="submit">Create {{$routeParts[0]}}</button>
        </div>
    </form>


<!---- Create Unit Validation ------------>

<script>
    $(document).ready(function() {
        const $rent = $("#rent");
        const $labelrent = $("#label-rent");
        const $labelsecurity = $("#label-security_deposit");
        const $security = $("#security_deposit");
        const $labelsellingprice = $("#label-selling_price");
        const $sellingprice = $("#selling_price");
        $labelsellingprice.hide();
        $sellingprice.hide();
        $('#unit_type').on('change', function() {
            var query = this.value;
            
            if (query === "sale") {
                $rent.hide();
                $labelrent.hide();

                $labelsecurity.hide();
                $security.hide();
                $labelsellingprice.show();
                $sellingprice.show();
            }else if(query === "rent"){
                $rent.show();
                $labelrent.show();

                $labelsecurity.show();
                $security.show();
                $labelsellingprice.hide();
                $sellingprice.hide();
            }

        });
    });
</script>
<h5 style="text-transform: capitalize;"><b> New {{ $routeParts[0] }}</b></h5>
<hr>
<form method="POST" action="{{ url($routeParts[0]) }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <input type="hidden" name="model_name" value="{{ ucfirst($routeParts[0]) }}">
    <div class="row">
        <!-- Left Column: Other Inputs -->
        <div class="col-md-6" style="padding-right:30px">
            @if($information)
            <div class="media alert-info mb-3 p-3">
                <i class="ti-info-alt icon-md text-warning d-flex align-self-start me-2 mb-3" style="color:#ffaf00"></i>
                <div class="media-body">
                    <p class="card-text">{{$information}}.</p>
                </div>
            </div>
            @endif

            @foreach($fields as $field => $attributes)
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
                    <option value=""> Select Value


                    </option>
                    @foreach ($data[$field] as $key => $value)
                    <option value="{{ $key }}" {{ old($field) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                <img id="logo-image-before-upload" src="{{ url('uploads/images/noimage.jpg') }}" style="height: 200px; width: 300px;">
                <!---- NUMBER INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif>
                @elseif($attributes['inputType'] === 'textarea')
                <!---- TEXTAREA INPUT ------------->
                <textarea class="form-control" id="exampleTextarea1" name="{{ $field }}" rows="3" columns="5" @if($attributes['required']) required @endif>

                </textarea>

                <!---- EMAIL INPUT ------------->
                @elseif($attributes['inputType'] === 'number')
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid  @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif>

                <!-------  MONEY INPUT ------------->
                @elseif($attributes['inputType'] === 'money')
                <div class="input-group" id="{{ $field }}">
                    <span class="input-group-text spanmoney">{{$sitesettings->site_currency}}</span>
                    <input type="number" class="form-control money  @error($field) is-invalid  @enderror" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif @if($attributes['readonly']) readonly @endif>
                </div>
                <!---- NORMAL INPUT ------------->

                @else
                <input type="{{ $attributes['inputType'] }}" class="form-control @error($field) is-invalid  @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($attributes['required']) required @endif @if($attributes['readonly']) readonly @endif>
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
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="submit">
            <i class="mdi mdi-content-save ms-1"></i>
            Create {{$routeParts[0]}}
        </button>
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
            $(".money").val('');

            if (query === "sale") {
                $rent.hide();
                $labelrent.hide();

                $labelsecurity.hide();
                $security.hide();
                $labelsellingprice.show();
                $sellingprice.show();
            } else if (query === "rent") {
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
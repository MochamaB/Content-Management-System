<div class=" contwrapper">


    <h4 style="text-transform: capitalize;">All {{$className = class_basename($setting->model_type)}} Settings &nbsp;
        @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <a href="#" class="editLink">Edit</a>
    </h4>
    @endif
    <hr>
    <div class="row">
        @foreach($globalSettings as $setting)
        <div class="col-md-6"> <!-- Each form wrapped in a col-md-5 div -->
            <form method="POST" action="{{ url($routeParts[0].'/'.$setting->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div> <!-- Additional div with red border (optional) -->
                    <div class="form-group">
                        <label class="label">{{$setting->name}}</label>
                        <h6>
                            <span class="requiredlabel">*</span>{{$setting->description}}
                        </h6>
                        <h5>
                            <small class="text-muted">
                                {{$setting->value}}
                            </small>
                        </h5>
                        @if($setting->key === 'massupdate')
                        <select class="formcontrol2" id="" name="value">
                            <option value="">Select Value</option>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                        @else
                        <input type="text" name="value" value="{{$setting->value}}" class="form-control" />
                        @endif

                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit {{$routeParts[0]}}</button>
                    </div>
                    <hr>
                </div>
            </form>
        </div>
        @endforeach
    </div>



</div>
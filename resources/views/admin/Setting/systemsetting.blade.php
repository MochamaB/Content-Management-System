
<div class=" contwrapper">
    <h4 style="text-transform: capitalize;"> {{ $groupName }} Settings &nbsp;
        @if( Auth::user()->can('setting.system') || Auth::user()->id === 1)
        <a href="" class="editLink"> Edit</a>
    </h4>
    @endif
    <hr>
    <form method="POST" action="{{ url('system-setting/update') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
         @method('PUT') <!-- or @method('PATCH') -->
        @foreach ($variables as $key => $value)
        <div class="col-md-6">
            <div class="form-group">

                <label class="label">{{ $key }}

                </label>
                <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{$value}}
                    </small>
                </h5>

                <input type="text" class="form-control" id="" value="{{ $value }}" name="{{ $key }}">
                </br>

            </div>
        </div>
        @endforeach
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" style="text-transform: capitalize;" id="submitBtn">Edit System Settings</button>
        </div>
    </form>

</div>




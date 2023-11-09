<div class="row">

    <div class="col-md-8">
        <div class=" contwrapper">

            <form method="POST" action="{{ url($routeParts[0]) }}" class="myForm" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @foreach($globalSettings as $setting)
                <h4 style="text-transform: capitalize;">{{$setting->setting_name}} &nbsp;
                    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                    <a href="#" class="editLink">Edit</a>
                </h4>
                @endif
                <hr>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Setting Name</label>
                        <h5>
                            <small class="text-muted">
                                {{$setting->setting_name}}
                            </small>
                        </h5>
                        <input type="text" name="setting_name" value="{{$setting->setting_name}}" class="form-control" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">Setting Value</label>
                        <h5>
                            <small class="text-muted">
                                {{$setting->setting_value}}
                            </small>
                        </h5>
                        <input type="text" name="setting_value" value="{{$setting->setting_value}}" class="form-control" />
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group">
                        <label class="label">Setting Description</label>
                        <h5>
                            <small class="text-muted">
                                {{$setting->setting_description}}
                            </small>
                        </h5>
                        <input type="text" name="setting_description" value="{{$setting->setting_description}}" class="form-control" />
                    </div>
                </div>

                @endforeach

        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Global settings
            </div>
            <div class="card-body">
                @if($globalSettings->isEmpty())
                <h5>
                    <small class="text">
                        No Settings added
                    </small>
                </h5>
                @endif
                <a href="{{ url('setting/create',['model' => $model ?? '']) }}" class="btn btn-primary btn-lg text-white mb-0 me-0 float-start" role="button" style="text-transform: capitalize;">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    Add New
                </a>
            </div>
        </div>

    </div>
</div>

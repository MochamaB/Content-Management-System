


        <div class=" contwrapper">

            <form method="POST" action="{{ url($routeParts[0]) }}" class="myForm" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
               
                <h4 style="text-transform: capitalize;">{{$setting->name}} &nbsp;
                    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
                    <a href="#" class="editLink">Edit</a>
                </h4>
                @endif
                <hr>
                @foreach($globalSettings as $setting)
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label">{{$setting->key}}</label>
                        <h5>
                            <small class="text-muted">
                                {{$setting->value}}
                            </small>
                        </h5>
                        <input type="text" name="value" value="{{$setting->value}}" class="form-control" />
                    </div>
                </div>
           
                @endforeach

        </div>


@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <form method="POST" action="{{ url('work-order') }}" class="myForm" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label class="label">{{$tickets->category}} Request</label>
                    <h5>
                        <small class="text-muted">
                        {{$tickets->subject}}
                        </small>
                    </h5>
                    <input type ="hidden" name="ticket_id" value="{{$tickets->id}}"/>
                   
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Raised By {{ $tickets->users->roles->first()->name }}</label>
                    <h5>
                        <small class="text-muted">
                        {{ $tickets->users->firstname }} {{ $tickets->users->lastname }}
                        </small>
                    </h5>
                </div>
            </div>


            <div class="col-md-7">
                <div class="form-group">
                    <label class="label">Description</label>
                    <h5>
                        <small class="text-muted">
                            {{ $tickets->description }}
                        </small>
                    </h5>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Priority</label>
                    <h5>
                        <small class="text-muted">
                            {{ $tickets->priority }}
                        </small>
                    </h5>
                </div>
            </div>

        </div>
        <hr>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label"> NOTES <span style="text-transform:capitalize"> (Enter brief description of work done)</span></label>
                <textarea name="notes" class="form-control" id="exampleTextarea1" rows="4" columns="6"></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="submit">Add Work Order Item</button>
        </div>
    </form>
</div>
@endsection
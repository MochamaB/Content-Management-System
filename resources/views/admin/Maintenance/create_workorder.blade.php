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
                    <input type="hidden" name="ticket_id" value="{{$tickets->id}}" />


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

                        @if($tickets->priority ==='critical')
                        <small class="text-muted text-error" style="text-transform:uppercase;font-weight:700">
                            <span class="statusdot statusdot-error"></span> {{ $tickets->priority }}</small>
                        @elseif($tickets->priority ==='high')
                        <small class="text-muted text-warning" style="text-transform:uppercase;font-weight:700">
                            <span class="statusdot statusdot-warning"></span> {{ $tickets->priority }}</small>
                        @elseif($tickets->priority ==='normal')
                        <small class="text-muted text-active" style="text-transform:uppercase;font-weight:700">
                            <span class="statusdot statusdot-active"></span>{{ $tickets->priority }}</small>
                        @else($tickets->priority ==='low')
                        <small class="text-muted text-dark" style="text-transform:uppercase;font-weight:700">
                            <span class="statusdot statusdot-dark"></span>{{ $tickets->priority }}</small>
                        @endif
                    </h5>
                </div>
            </div>

        </div>
        <hr>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Assign To &nbsp;&nbsp;&nbsp;
                    @if( Auth::user()->can('ticket.assign') || Auth::user()->id === 1)
                    <a class="" href="{{ url('ticket/assign/'.$tickets->id) }}"> <i class="mdi mdi-lead-pencil text-primary"></i></a>
                    @endif
                </label>
                <input type="hidden" name="user_id" value="{{$tickets->assigned_id}}">
                <h5>
                   
                <small class="text-muted">
                @if($tickets->assigned_type === 'App\\Models\\User')
                    {{$tickets->assigned->firstname}} {{$tickets->assigned->lastname}} - ( {{$tickets->assigned->roles->first()->name}})
                    @else
                    {{$tickets->assigned->name}} - Vendor
                    @endif
                </small>
                </h5>
            </div>
        </div>
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
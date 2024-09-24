@php
$statusClasses = [
'completed' => 'active',
'New' => 'warning',
'OverDue' => 'error',
'In Progress' => 'information',
'Assigned' => 'dark',
];
@endphp
<div class="row flex-grow">
    <div class="col-12 grid-margin stretch-card">
        <!-- TICKETS ----------------->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title card-title-dash">Recent Tickets</h4>
                            
                        </div>
                        <div class="list-wrapper">
                            <ul class="todo-list todo-list-rounded">

                                @foreach($tickets as $ticket)
                                <li class="d-block">
                                    <div class="form-check w-100 mt-1 mb-2">
                                        <label class="form-check-label">
                                            <b>{{$ticket->category}}:</b>
                                            {{$ticket->subject}}
                                        </label>
                                        <div class="d-flex mt-0 mb-2">
                                            <div class="ps-4 text-small me-3">
                                                {{ \Carbon\Carbon::parse($ticket->created_at)->format('Y M') }}
                                            </div>
                                            <div class="badge badge-{{ $statusClasses[$ticket->status] ?? 'secondary' }} me-3">
                                                {{ $ticket->status }}
                                            </div>
                                            <i class="mdi mdi-flag ms-2 flag-color"></i>
                                        </div>
                                    </div>
                                </li>
                                @endforeach

                            </ul>
                            <div class="text-center mt-3">
                                <h6>
                                <a class="" href="{{ url('/ticket') }}">
                                    @if(isset($ticket))
                                    <p>View More</p>
                                    @else
                                    <p>Add Ticket</p>
                                    @endif
                                </a></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
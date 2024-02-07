<div class="row">
    <div class="col-md-7">

        <div class=" contwrapper">
            @include('admin.CRUD.edit')

        </div>

    </div>


    <div class="col-md-5">
        <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">
            <h4><b> Process Requests</b>
            </h4>
            <hr>
            <h5>Status:</h5>
            @if ($modelrequests)
            @php
            $statusClasses = [
            'Completed' => 'active',
            'New' => 'warning',
            'OverDue' => 'error',
            'In Progress' => 'information',
            'Reported' => 'dark',
            ];

            // Get the status and find its corresponding class
            $status = $modelrequests->status;
            $statusClass = $statusClasses[$status] ?? 'default';
            @endphp

            <span class="statusdot statusdot-{{ $statusClass }}"></span>
            <span>{{ $status }}</span>
            @else
            <!-- Handle the case when $modelrequest is null -->
            <span class="statusdot statusdot-default"></span>
            <span>N/A</span>
            @endif
            <hr>
            <h5>Assigned:</h5>
            @if ($modelrequests->assigned)
            <!-- Assigned user or vendor exists -->
            @if ($modelrequests->assigned_type === 'user')
            <!-- Assigned to a user -->
            <span>{{ $modelrequests->assigned->name }}</span>
            @elseif ($modelrequests->assigned_type === 'vendor')
            <!-- Assigned to a vendor -->
            <span>{{ $modelrequests->assigned->vendor_name }}</span>
            @endif
            @else
            <!-- No assigned user or vendor -->
            <a href="{{ url('request/assign/'.$modelrequests->id) }}">Assign Request</a>
            @endif
            <hr>
            <h5>Total Invoice Amount:</h5>
            <h5>Total Paid Amount:</h5>


        </div>

    </div>
</div>
@include('layouts.admin.scripts')